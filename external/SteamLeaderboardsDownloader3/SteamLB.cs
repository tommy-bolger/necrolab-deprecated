using System;
using System.IO;
using System.Text;

using SteamKit2;

namespace SteamLB
{
    class Program
    {
        static SteamClient steamClient;
        static CallbackManager manager;

        static SteamUser steamUser;
        static SteamUserStats steamUserStats;

        static bool isRunning;

        static bool loggedIn;

        static string user, pass;

        static int batchSize = 10000;

        static void Main(string[] args)
        {
            if (args.Length < 4)
            {
                Console.WriteLine("usage: SteamLB.exe <username> <password> <appid> <leaderboardListFile>");
                return;
            }

            Run(args);
            steamClient.Disconnect();
        }

        static void Run(string[] args)
        {
            user = args[0];
            pass = args[1];

            steamClient = new SteamClient(System.Net.Sockets.ProtocolType.Tcp);
            manager = new CallbackManager(steamClient);

            steamUser = steamClient.GetHandler<SteamUser>();
            steamUserStats = steamClient.GetHandler<SteamUserStats>();

            manager.Subscribe<SteamClient.ConnectedCallback>(OnConnected);
            manager.Subscribe<SteamClient.DisconnectedCallback>(OnDisconnected);
            manager.Subscribe<SteamUser.LoggedOnCallback>(OnLoggedOn);
            manager.Subscribe<SteamUser.LoggedOffCallback>(OnLoggedOff);

            isRunning = true;

            Console.WriteLine("Connecting to Steam...");

            loggedIn = false;

            SteamDirectory.Initialize().Wait();

            steamClient.Connect();

            while (!loggedIn)
            {
                manager.RunWaitCallbacks(TimeSpan.FromSeconds(1));
                if (!isRunning)
                {
                    return;
                }
            }

            uint appid = Convert.ToUInt32(args[2]);
            string lbListFile = args[3];

            foreach (string lbname in File.ReadLines(lbListFile))
            {
                if (!String.IsNullOrEmpty(lbname) && isRunning)
                {
                    DownloadLeaderboard(appid, lbname);
                }
            }
        }

        static EResult DownloadLeaderboard(uint appid, string lbname)
        {
            Console.WriteLine($"Locating leaderboard {lbname} for appID {appid}...");

            var findLeaderboardJob = steamUserStats.FindLeaderboard(appid, lbname);

            while (findLeaderboardJob.GetAwaiter().IsCompleted)
            {
                manager.RunWaitCallbacks(TimeSpan.FromSeconds(1));
                if (!isRunning)
                {
                    return EResult.Fail;
                }
            }

            var leaderboardResult = findLeaderboardJob.GetAwaiter().GetResult();

            if (leaderboardResult.Result != EResult.OK)
            {
                Console.Error.WriteLine($"Failed to locate leaderboard {lbname} for appID {appid}! Error code: {leaderboardResult.Result.ToString()}");
                return leaderboardResult.Result;
            }

            if (leaderboardResult.ID == 0)
            {
                Console.Error.WriteLine($"Failed to locate leaderboard {lbname} for appID {appid}! ID returned was 0");
                return EResult.Fail;
            }

            var firstEntry = 1;
            var lastEntry = leaderboardResult.EntryCount;

            var downloadEntryCount = lastEntry - firstEntry + 1;

            Console.WriteLine($"Found leaderboard {lbname}.");
            Console.WriteLine($"Preparing to download {downloadEntryCount} entries...");

            var outputFilename = $"{leaderboardResult.ID}.csv";

            using (var outputWriter = new StreamWriter(File.Open(outputFilename, FileMode.Create)))
            {
                outputWriter.WriteLine(lbname);

                for (int i = 0; i < downloadEntryCount / batchSize; ++i)
                {
                    DownloadLeaderboardRange(appid, leaderboardResult.ID, i * batchSize + firstEntry, firstEntry + (i + 1) * batchSize - 1, downloadEntryCount, outputWriter);
                }

                if (downloadEntryCount % batchSize != 0)
                {
                    DownloadLeaderboardRange(appid, leaderboardResult.ID, (downloadEntryCount / batchSize) * batchSize + firstEntry, lastEntry, downloadEntryCount, outputWriter);
                }

                Console.WriteLine($"Done! Results for {lbname} written to {outputFilename}");
            }

            return EResult.OK;
        }

        static EResult DownloadLeaderboardRange(uint appid, int lbid, int first, int last, int totalCount, StreamWriter outputWriter)
        {
            Console.WriteLine($"Downloading entries {first} to {last}... [{(first * 100) / totalCount}%]");

            var queryLeaderboardEntriesJob = steamUserStats.GetLeaderboardEntries(appid, lbid, first, last, ELeaderboardDataRequest.Global);

            while (queryLeaderboardEntriesJob.GetAwaiter().IsCompleted)
            {
                manager.RunWaitCallbacks(TimeSpan.FromSeconds(1));
                if (!isRunning)
                {
                    return EResult.Fail;
                }
            }

            var result = queryLeaderboardEntriesJob.GetAwaiter().GetResult();

            if (result.Result == EResult.OK)
            {
                foreach (var entry in result.Entries)
                {
                    var line = new StringBuilder();
                    line.Append(entry.SteamID.ConvertToUInt64());
                    line.Append(',');
                    line.Append(entry.GlobalRank);
                    line.Append(',');
                    line.Append(entry.Score);
                    line.Append(',');
                    line.Append(entry.UGCId.Value);
                    foreach (var detail in entry.Details)
                    {
                        line.Append(',');
                        line.Append(detail);
                    }
                    outputWriter.WriteLine(line);
                }
            }
            else
            {
                Console.Error.WriteLine($"Failed to download leaderboard section! Error code: {result.Result.ToString()}");
            }

            return result.Result;
        }

        static void OnConnected(SteamClient.ConnectedCallback callback)
        {
            if (callback.Result != EResult.OK)
            {
                Console.WriteLine("Unable to connect to Steam: {0}", callback.Result);

                isRunning = false;
                return;
            }

            Console.WriteLine("Connected to Steam! Logging in '{0}'...", user);

            steamUser.LogOn(new SteamUser.LogOnDetails
            {
                Username = user,
                Password = pass,
            });
        }

        static void OnDisconnected(SteamClient.DisconnectedCallback callback)
        {
            Console.WriteLine("Disconnected from Steam");

            isRunning = false;
        }

        static void OnLoggedOn(SteamUser.LoggedOnCallback callback)
        {
            if (callback.Result != EResult.OK)
            {
                if (callback.Result == EResult.AccountLogonDenied)
                {
                    Console.WriteLine("Unable to logon to Steam: This account is SteamGuard protected.");

                    isRunning = false;
                    return;
                }

                Console.WriteLine($"Unable to logon to Steam: {callback.Result.ToString()} / {callback.ExtendedResult}");

                isRunning = false;
                return;
            }

            Console.WriteLine("Successfully logged on!");
            loggedIn = true;
        }

        static void OnLoggedOff(SteamUser.LoggedOffCallback callback)
        {
            Console.WriteLine("Logged off of Steam: {callback.Result.ToString()}");
        }
    }
}
