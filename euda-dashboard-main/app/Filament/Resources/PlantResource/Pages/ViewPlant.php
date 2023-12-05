<?php

namespace App\Filament\Resources\PlantResource\Pages;

use App\Filament\Resources\PlantResource;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Storage;
use App\Models\Plant;
use App\Models\Message;
use App\Models\State;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use App\Models\Command;
use App\Models\Debug; 



class ViewPlant extends EditRecord
{

    protected static string $resource = PlantResource::class;

    protected static string $view = 'filament.resources.plant-resource.pages.view-plant';

    public $status;
    public $awsData;
    public $selectedDate;
    public $totalOutService;
    public $lastOutService;
    public $lastDoorFault;
    public $totalDoorFault;
    public $totalSTR;
    public $lastSTR;
    public $totalDoorOpenings;
    public $totalRides;
    public $lastDoorFaultFloor;
    public $FCN;
    public $DFD;
    public $currentFloor;
    public $AC;
    public $BAT;
    public $CLS;
    public $alarms;
    public $lastcommunication;
    public $CAM;
    public $DON;
    public $versionInstalled;




    protected function getHeader(): View
    {
        return view('filament.resources.plant-resource.pages.components.header', ['plant' => $this->getRecord()]);
    }

    public function mount($record): void
    {
        parent::mount($record);
        // Initializa variable
        $this->form->fill([
            'date' => Carbon::today(),
        ]);
        $this->selectedDate = Carbon::today()->format("Y-m-d");
        $this->initVariable();
    }

    protected function getFormSchema(): array
    {
        // Setting data picker
        return [
            DatePicker::make('date')->label('')->reactive()
            ->afterStateUpdated(function (Closure $set, $state) {
                $this->selectedDate = Carbon::parse($state)->format("Y-m-d");
                //error_log($this->selectedDate);
                $this->goToDate();
            }),
        ];
    }

   
    public function getData()
    {
     
        $messagesData =$this->getStates();
        $latestDebug = $this->getLatestDebug();
        if ($latestDebug) {
            $this->setVersionInstalled($latestDebug);
        }
        $erm = $this->getErmData();
        $err = $this->getErrData();
        $lastErm = $this->getLastErm();
        $lastErr = $this->getLastErr();
        $ermCount = $erm->count();

        $messages = json_decode($messagesData);
        if (empty($messages))
            return 0;
        $awsObject = null;
        $eventData = Event::whereDate('created_at', $this->selectedDate)
            ->where('plantId', $this->record->plant_id)
            ->get();
        $lastEvent = Event::whereDate('created_at', $this->selectedDate)
            ->where('plantId', $this->record->plant_id)
            ->latest('created_at')
            ->first();
    
        foreach ($messages as $k => $data) {
                if($lastEvent != null){
                    $eventIUPS = json_decode($lastEvent->IUPS, true);
                    $this->currentFloor = $eventIUPS['f'];

                }else{
                    $this->currentFloor = json_decode($data->IUPS)->f;
                }
                $data->FCN = json_decode($data->FCN);
                foreach ($eventData as $event) {
                    if (is_array($eventIUPS) && array_key_exists('f', $eventIUPS) && is_numeric($eventIUPS['f'])) {
                        $index = (int) $eventIUPS['f'] - 1;
                        if ($index >= 0 && $index < count($data->FCN)) {
                            $data->FCN[$index]++;
                        }
                    }   
                }
                $this->FCN = $data->FCN;
                $this->statusCheck($data->event && $data->event->OOS ? $data->event->OOS : $data->OOS);
                $this->alarms = [
                    "AC" => $data->event && property_exists($data->event, 'AC') ? $data->event->AC : $data->AC,
                    "BAT" => $data->event && property_exists($data->event, 'BAT') ? $data->event->BAT : $data->BAT,
                    "CLS" => $data->event && property_exists($data->event, 'CLS') ? $data->event->CLS : true,
                    "OOS" => $data->event && property_exists($data->event, 'OOS') ? $data->event->OOS : $data->OOS
                ];
                $this->CAM = $data->CAM/1000;
                $this->DON =$data->DON;
                $this->lastcommunication = $data->event ? formatDate($data->event->updated_at) : formatDate($data->updated_at);
              
                if (!$awsObject) {
                    $awsObject = [
                        'id' => $data->id,
                        'plantId' => $data->plantId,
                        'OOS' => $data->event && $data->event->OOS ? $data->event->OOS : $data->OOS,
                        'OSN' => $data->OSN,
                        'FCN' => $data->FCN,
                        'CAM' => $data->CAM/1000,
                        'STR' => $lastRecord->STR ?? 0,
                        'totalSTR' => $this->totalSTR,
                        'IUPS' => $data->IUPS,
                        'DON' => $data->DON,
                        'BAT' => $data->event->BAT ?? $data->BAT,
                        'AC'  => $data->event->AC ?? $data->AC,
                        'CLS' => $data->event->CLS ?? false,
                        'rides' => $data->rides,
                        'total_erm' =>  $ermCount,
                        'lastErm_date' => $lastErm ? formatDate($lastErm['created_at']) : $this->lastFSR,
                        'total_err' => $err ? $err->count(): 0,
                        'lastErr_date' => $lastErr ? formatDate($lastErr['created_at']) : $this->lastFSR,
                        'created_at' => $data->event->created_at ?? $data->created_at,
                        'updated_at' => $data->event ? formatDate($data->event->updated_at) : formatDate($data->updated_at),
                    ];
            
                    if (empty($awsObject['DFD'])) {
                        $awsObject['DFD'] = array_fill(0, count($awsObject['FCN']), 0);
                    }
                } else {
                    
                    $awsObject['OSN'] += $data->OOS;
                    if($k == count($messages)-1){
                        $awsObject['OOS'] = $data->OSS ?? 0;
                    }
            
                    foreach ($data->FCN as $key => $value) {
                        $awsObject['FCN'][$key] += $value;
                    }
            
                    $awsObject['CAM'] += $data->CAM/1000;
                    $awsObject['DON'] += $data->DON;
                    $awsObject['rides'] += $data->rides;
                    $awsObject['sequence'] = $data->sequence;
                    $awsObject['created_at'] = $data->created_at;
                    $awsObject['updated_at'] = $data->updated_at;  
                }
            
            }

        $this->totalDoorFault = 0;
        $this->totalOutService = 0;
        if($eventData->isNotEmpty()){
            foreach ($eventData as $event) {
                $DFDArray = json_decode($event->DFD, true);
               if (is_array($DFDArray) && !empty($DFDArray) && count(array_filter($DFDArray, function($value) { return $value !== 0; })) > 0) {
                    foreach ($DFDArray as $key => $value) {
                        if ($value == 2) {
                            //dd($value, $key, $DFDArray);die();
                            $this->totalDoorFault++;
                            $this->lastDoorFaultFloor = $key;
                            $this->lastDoorFault = $event->created_at; // Assuming created_at is the time you want
                        }
                    }
                }
                if($event->OOS == 1){
                    $this->totalOutService++;
                    $this->lastOutService = $event->created_at;
                }
            }
           
        }
        $this->FCN = $awsObject['FCN'];

        //dd($this->FCN);die();
        return json_encode($awsObject);
    }


    private function getStates()
    {
        return State::whereDate('created_at', $this->selectedDate)
            ->where('plantId', $this->record->plant_id)
            ->with(['event' => function ($query) {
                $query->whereDate('created_at', $this->selectedDate);
            }])
            ->get()
            ->toJson();
    }

    private function getLatestDebug()
    {
        return Debug::where('plantId', $this->record->plant_id)->latest()->first();
    }

    private function getErmData()
    {
        return Command::whereDate('created_at', $this->selectedDate)
            ->whereNotNull('ERM')
            ->where('plantId', $this->record->plant_id)
            ->get();
    }

    private function getErrData()
    {
        return Command::whereDate('created_at', $this->selectedDate)
            ->whereNotNull('ERR')
            ->where('ERR', true)
            ->where('plantId', $this->record->plant_id)
            ->get();
    }

    private function getLastErm()
    {
        return Command::whereDate('created_at', $this->selectedDate)
            ->whereNotNull('ERM')
            ->where('plantId', $this->record->plant_id)
            ->latest()
            ->first();
    }

    private function getLastErr()
    {
        return Command::whereDate('created_at', $this->selectedDate)
            ->whereNotNull('ERR')
            ->where('ERR', true)
            ->where('plantId', $this->record->plant_id)
            ->latest()
            ->first();
    }



//     public function getData()
//     {
     
//         $messagesData =$this->getStates();
//         $latestDebug = $this->getLatestDebug();
//         if ($latestDebug) {
//             $this->setVersionInstalled($latestDebug);
//         }
//         $erm = $this->getErmData();
//         $err = $this->getErrData();
//         $lastErm = $this->getLastErm();
//         $lastErr = $this->getLastErr();
//         $ermCount = $erm->count();
//         $messages = json_decode($messagesData);
//         if (empty($messages))
//             return 0;
//         $awsObject = null;
//         $eventData = $this->getEventData();
//         $this->totalDoorFault = 0;
//         $this->totalOutService = 0;
//         $this->processEventData($eventData);

//         foreach ($messages as $k => $data) {
//             $data->FCN = json_decode($data->FCN);
//             $this->processIUPS($data, $eventData);
//             $this->processAlarms($data);
        
//             $awsObject = $this->buildAwsObject($data, $awsObject);
//         }

//         foreach ($messages as $k => $data) {
        
//             $this->processIUPS($data, $eventData);
//             $this->processAlarms($data);
        
//             $awsObject = $this->buildAwsObject($data, $awsObject);
//         }
       
//         $awsObject->total_erm = $ermCount;
//         $awsObject->lastErm_date = $lastErm ? $this->formatDate($lastErm->created_at) : "No";
//         $awsObject->total_err = $err ? $err->count() : 0;
//         $awsObject->lastErr_date = $lastErr ? $this->formatDate($lastErr->created_at) : "No";

//         return json_encode($awsObject);
//     }

    

//     private function getEvents()
//     {
//         return Event::whereDate('created_at', $this->selectedDate)
//             ->where('plantId', $this->record->plant_id)
//             ->latest('created_at')
//             ->get();
//     }
//     private function getLatestDebug()
//     {
//         return Debug::where('plantId', $this->record->plant_id)->latest()->first();
//     }

//     private function setVersionInstalled()
//     {
//         $latestDebug = Debug::where('plantId', $this->record->plant_id)->latest()->first();
//         $this->versionInstalled = $latestDebug ? $latestDebug->fwMajor.'.'.$latestDebug->fwMinor.'.'.$latestDebug->fwPatch : 'null';
//     }
//     private function getErmData()
//     {
//         return Command::whereDate('created_at', $this->selectedDate)
//             ->whereNotNull('ERM')
//             ->where('plantId', $this->record->plant_id)
//             ->get();
//     }

//     private function getErrData()
//     {
//         return Command::whereDate('created_at', $this->selectedDate)
//             ->whereNotNull('ERR')
//             ->where('ERR', true)
//             ->where('plantId', $this->record->plant_id)
//             ->get();
//     }

//     private function getLastErm()
//     {
//         return Command::whereDate('created_at', $this->selectedDate)
//             ->whereNotNull('ERM')
//             ->where('plantId', $this->record->plant_id)
//             ->latest()
//             ->first();
//     }

//     private function getLastErr()
//     {
//         return Command::whereDate('created_at', $this->selectedDate)
//             ->whereNotNull('ERR')
//             ->where('ERR', true)
//             ->where('plantId', $this->record->plant_id)
//             ->latest()
//             ->first();
//     }

//     private function getEventData()
//     {
//         return Event::whereDate('created_at', $this->selectedDate)
//             ->where('plantId', $this->record->plant_id)
//             ->latest('created_at')
//             ->get();
//     }
//     private function processEventData($eventData)
//     {
//         foreach ($eventData as $event) {
//             $DFDArray = json_decode($event->DFD, true);
//             if (is_array($DFDArray) && !empty($DFDArray) && count(array_filter($DFDArray, function($value) { return $value !== 0; })) > 0) {
//                 foreach ($DFDArray as $key => $value) {
//                     if ($value == 2) {
//                         $this->totalDoorFault++;
//                         $this->lastDoorFaultFloor = $key;
//                         $this->lastDoorFault = $event->created_at;
//                     }
//                 }
//             }
//             if($event->OOS == 1){
//                 $this->totalOutService++;
//                 $this->lastOutService = $event->created_at;
//             }
//         }
//     }

//     private function processMessage($data, $eventData)
//     {
//         $data->FCN = json_decode($data->FCN);
//         if($eventData->isNotEmpty()){
//             foreach ($eventData as $event) {
//                 $eventIUPS = json_decode($event->IUPS, true);
//                 $this->IUPS = $eventIUPS;
//                 if (is_array($eventIUPS) && array_key_exists('f', $eventIUPS) && is_numeric($eventIUPS['f'])) {
//                     $index = (int) $eventIUPS['f'] - 1;
//                     if ($index >= 0 && $index < count($data->FCN)) {
//                         $data->FCN[$index]++;
//                     }
//                 }
//             }
//         }   
//         if($this->IUPS == null){
//             $this->IUPS = $data->IUPS;
//         }
//     }

//     private function processIUPS($data, $eventData)
//     {
//         //dd($data);die();
       
//         if ($eventData->isNotEmpty()) {
//             foreach ($this->eventData as $event) {
//                 $eventIUPS = json_decode($event->IUPS, true);
//                 $this->IUPS = $eventIUPS;

//                 if ($this->isNumericArrayKeyExists($eventIUPS, 'f')) {
//                     $index = (int) $eventIUPS['f'] - 1;

//                     if ($this->isValidIndex($index, $data->FCN)) {
//                         $data->FCN[$index]++;
//                     }
//                 }
//             }
//         }

//         if ($this->IUPS == null) {
//             $this->IUPS = $data->IUPS;
//         }
//     }

// private function processAlarms($data)
// {
//     $this->FCN = $data->FCN;
//     $this->statusCheck($this->getEventDataProperty($data, 'event.OOS', $data->OOS));

//     $this->alarms = [
//         "AC"  => $this->getEventDataProperty($data, 'event.AC', $data->AC),
//         "BAT" => $this->getEventDataProperty($data, 'event.BAT', $data->BAT),
//         "CLS" => $this->getEventDataProperty($data, 'event.CLS', true),
//         "OOS" => $this->getEventDataProperty($data, 'event.OOS', $data->OOS),
//     ];

//     $this->CAM = $data->CAM / 1000;
//     $this->DON = $data->DON;
//     $this->lastcommunication = $this->getEventDataProperty($data, 'event.updated_at', $data->updated_at);
// }

// private function getEventDataProperty($data, $property, $isEvent = false)
// {
//     $eventProperty = $isEvent ? 'event' : null;

//     if (
//         isset($data->$eventProperty) &&
//         is_object($data->$eventProperty) &&
//         property_exists($data->$eventProperty, $property)
//     ) {
//         return $data->$eventProperty->$property;
//     } elseif (property_exists($data, $property)) {
//         return $data->$property;
//     } else {
//         return null; // or handle the case when the property is not found
//     }
// }


// private function buildAwsObject($data, $awsObject)
// {
//     if (!$awsObject) {
//         $awsObject = (object) [
//             'id' => $data->id,
//             'plantId' => $data->plantId,
//             'OOS' => $data->event && $data->event->OOS ? $data->event->OOS : $data->OOS,
//             'OSN' => $data->OSN,
//             'FCN' => $data->FCN,
//             'CAM' => $data->CAM/1000,
//             'STR' => $lastRecord->STR ?? 0,
//             'totalSTR' => $this->totalSTR,
//             'DFD' => json_decode($data->event->DFD ?? $data->DFN),
//             'IUPS' => json_decode($data->event->IUPS ?? $data->IUPS),
//             'DON' => $data->DON,
//             'BAT' => $data->event->BAT ?? $data->BAT,
//             'AC'  => $data->event->AC ?? $data->AC,
//             'CLS' => $data->event->CLS ?? false,
//             'rides' => $data->rides,
//             'sequence' => $data->sequence,
//             'created_at' => $data->event->created_at ?? $data->created_at,
//             'updated_at' => $data->event ? formatDate($data->event->updated_at) : formatDate($data->updated_at),
//         ];
    
//         if ($awsObject->DFD === "[]") {
//             $awsObject->DFD = [];
//         }

//         if (empty($awsObject->DFD)) {
//             $awsObject->DFD = array_fill(0, count($awsObject->FCN), 0);
//         }

//         $this->DFD = [
//             $this->getEventDataProperty($data, 'event.DFD', $awsObject->DFD),
//             json_decode($this->IUPS),
//         ];
//     } 

//     return $awsObject;
// }



    public function statusCheck($status) {
      
        if($status == 1)
            return 'fault';
        // else if($awsData->fOutOfService >= 1)
        //     return 'warning';
        else
            return 'active';
    }

    public function goToDate() {
        $this->initVariable();
    }

    public function initVariable() {
        $this->totalOutService = 0;
        $this->lastOutService = 'No out of service';
        $this->totalFSR = 0;
        $this->lastFSR = 'No tests';
        $this->lastDoorFault = 'No door fault';
        $this->lastDoorFaultFloor = '';
        $this->awsData = $this->getData();
        if($this->awsData)
            $this->status = $this->statusCheck($this->awsData);
        else
            Self::$view = 'filament.resources.plant-resource.pages.components.no-file-found';
    }
}