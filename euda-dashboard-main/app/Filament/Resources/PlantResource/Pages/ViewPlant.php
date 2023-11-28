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
    public $IUPS;
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
     
        $messagesData = State::whereDate('created_at', $this->selectedDate)
            ->where('plantId', $this->record->plant_id)
            ->with(['event' => function ($query) {
                $query->whereDate('created_at', $this->selectedDate);
            }])
            ->get()
            ->toJson();
        $latestDebug = Debug::where('plantId', $this->record->plant_id)->latest()->first();

        if ($latestDebug) {
            $versionInstalled = $latestDebug->fwMajor.'.'.$latestDebug->fwMinor.'.'.$latestDebug->fwPatch;
        
           
            cache(['versionInstalled' => $versionInstalled], );
        
            $this->versionInstalled = $versionInstalled;
        } else {
            // Handle the case where no matching record is found
            $this->versionInstalled = 'null';
        }
         
            $erm =  Command::whereDate('created_at', $this->selectedDate)->whereNotNull('ERM')->where('plantId', $this->record->plant_id)->get();
            $err =  Command::whereDate('created_at', $this->selectedDate) ->whereNotNull('ERR')->where('ERR', true)->where('plantId', $this->record->plant_id)->get();
            $lastErm = Command::whereDate('created_at', $this->selectedDate)
                ->whereNotNull('ERM')
                ->where('plantId', $this->record->plant_id)
                ->latest() 
                ->first(); 
            $lastErr = Command::whereDate('created_at', $this->selectedDate)
                ->whereNotNull('ERR')
                ->where('ERR', true)
                ->where('plantId', $this->record->plant_id)
                ->latest() 
                ->first();     
            $ermCount = $erm->count();
           
        $messages = json_decode($messagesData);
        if (empty($messages))
            return 0;
        $awsObject = null;
        $eventData = Event::whereDate('created_at', $this->selectedDate)
            ->where('plantId', $this->record->plant_id)
            ->latest('created_at')
            ->get();

            $this->totalDoorFault = 0;
            $this->totalOutService = 0;
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
        
        foreach ($messages as $k => $data) {

            $data->FCN = json_decode($data->FCN);
            
            foreach ($eventData as $event) {
                $eventIUPS = json_decode($event->IUPS, true);
                $this->IUPS = $eventIUPS;
                if (is_array($eventIUPS) && array_key_exists('f', $eventIUPS) && is_numeric($eventIUPS['f'])) {
                    $index = (int) $eventIUPS['f'] - 1;
            
                    if ($index >= 0 && $index < count($data->FCN)) {
                        $data->FCN[$index]++;
                    }
                }
                
            }
            if($this->IUPS == null){
                $this->IUPS = $data->IUPS;
            }
           
            //dd($this->IUPS);die();
            $this->FCN = $data->FCN;
            //dd($this->FCN);die();
            // $this->AC = $data->event->AC;
            // $this->BAT = $data->event->BAT;
            // $this->CLS = $data->event->CLS;
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
                $awsObject = (object)[
                    'id' => $data->id,
                    'plantId' => $data->plantId,
                    'OOS' => $data->event && $data->event->OOS ? $data->event->OOS : $data->OOS,
                    'OSN' => $data->OSN,
                    'FCN' => $data->FCN,
                    'CAM' => $data->CAM/1000,
                    'STR' => $lastRecord->STR ?? 0,
                    'totalSTR' => $this->totalSTR,
                    'DFD' => json_decode($data->event->DFD ?? $data->DFN),
                    'IUPS' => json_decode($data->event->IUPS ?? $data->IUPS),
                    'DON' => $data->DON,
                    'BAT' => $data->event->BAT ?? $data->BAT,
                    'AC'  => $data->event->AC ?? $data->AC,
                    'CLS' => $data->event->CLS ?? false,
                    'rides' => $data->rides,
                    'total_erm' =>  $ermCount,
                    'lastErm_date' => $lastErm ? formatDate($lastErm->created_at) : $this->lastFSR,
                    'total_err' => $err ? $err->count(): 0,
                    'lastErr_date' => $lastErr ? formatDate($lastErr->created_at) : $this->lastFSR,
                    'sequence' => $data->sequence,
                    'created_at' => $data->event->created_at ?? $data->created_at,
                    'updated_at' => $data->event ? formatDate($data->event->updated_at) : formatDate($data->updated_at),
                ];
                //dd($awsObject);die();
                if ($awsObject->DFD === "[]") {
                    // Convert the string representation of an empty array to an actual empty array
                    $awsObject->DFD = [];
                }
                
                if (empty($awsObject->DFD)) {
                    // DFD is an empty array, add FCN array count with all 0 values
                    $awsObject->DFD = array_fill(0, count($awsObject->FCN), 0);
                }
                $this->DFD = [
                    $data->event->DFD ?? $awsObject->DFD,
                    json_decode($this->IUPS)
                ];
           

                //dd($this->DFD);die();
            } else {
                $awsObject->OSN += $data->OOS;
                if($k == count($messages)-1){
                    //dd($data->OOS);die();
                    $awsObject->OOS = $data->OSS ?? 0;
                }
                $fcnArray =$data->FCN;
            
                foreach ($fcnArray as $key => $value) {
                    $awsObject->FCN[$key] += $value;
                }
                $awsObject->CAM += $data->CAM/1000;
                $awsObject->DON += $data->DON;
                $awsObject->rides += $data->rides;
                $awsObject->sequence = $data->sequence;
                $awsObject->created_at = $data->created_at;
                $awsObject->updated_at = $data->updated_at;  
            }
        }
        
        //dd($this->totalOutService);die();
        return json_encode($awsObject);
    }



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