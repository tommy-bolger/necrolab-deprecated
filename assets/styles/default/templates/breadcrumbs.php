<ol id="breadcrumbs" class="breadcrumb">
    <?php 
        if(!empty($this->breadcrumbs)) {
            $last_index = (count($this->breadcrumbs) - 1);
        
            foreach($this->breadcrumbs as $index => $breadcrumb) {
                $url = $breadcrumb['url'];
                $page_name = $breadcrumb['page_name'];
            
                if($index < $last_index) {
                    //echo "<li><a href=\"{$url}\">{$page_name}</a></li>";
                    echo "<li class=\"menu_small\">{$page_name}</li>";
                }
                else {
                    echo "<li class=\"menu_small active\">{$page_name}</li>";
                }
            }
        }
    ?>
</ol>