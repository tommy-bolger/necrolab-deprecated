<?php
/**
* The base class for ranking api endpoints in Necrolab.
* Copyright (c) 2017, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Modules\Necrolab\Controllers\Api\Players\Player\Rankings;

use \DateTime;
use \Modules\Necrolab\Controllers\Api\Players\Player\Player;

class Rankings
extends Player {
    protected $start_date;
    
    protected $end_date;
    
    public function init() {
        $this->setSteamidFromRequest();
    
        $this->setReleaseFromRequest();
    
        $this->getResultsetStateFromRequest();
    }

    protected function setDateRangeFromRequest() {    
        $start_date = request()->get->start_date;
        
        if(empty($start_date)) {
            $this->framework->outputManualError(400, "Required property 'start_date' was not found in the request.");
        }
        
        if(!empty($start_date)) {
            $start_date_object = DateTime::createFromFormat('Y-m-d', $start_date);
        
            if(!empty($start_date_object)) {
                $this->start_date = new DateTime($start_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->start_date)) {
            $this->framework->outputManualError(400, "Property 'start_date' is not a valid date. Please specify a date in the YYYY-MM-DD format.");
        }
        
        $end_date = request()->get->end_date;
        
        if(!empty($end_date)) {
            $end_date_object = DateTime::createFromFormat('Y-m-d', $end_date);
        
            if(!empty($end_date_object)) {
                $this->end_date = new DateTime($end_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->end_date)) {
            $this->end_date = new DateTime(date('Y-m-d'));
        }
        
        $this->request['start_date'] = $this->start_date->format('Y-m-d');
        $this->request['end_date'] = $this->end_date->format('Y-m-d');
    }
}