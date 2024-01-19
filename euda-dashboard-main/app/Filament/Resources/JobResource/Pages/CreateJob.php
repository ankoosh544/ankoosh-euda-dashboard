<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Filament\Resources\JobResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Aws\Iot\IotClient;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;
use Filament\Notifications\Notification;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;
    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin || Auth::user()->is_technician;
    }
    
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {

    //     dd($data);die();

    //     $iotClient = new IotClient([
    //         'version' => 'latest',
    //         'region' => env('AWS_DEFAULT_REGION'),
    //         'credentials' => [
    //             'key' => env('AWS_ACCESS_KEY_ID'),
    //             'secret' => env('AWS_SECRET_ACCESS_KEY'),
    //         ],
    //     ]);

    //     $s3Client = new S3Client([
    //         'version' => 'latest',
    //         'region' => env('AWS_DEFAULT_REGION'),
    //         'credentials' => [
    //             'key' => env('AWS_ACCESS_KEY_ID'),
    //             'secret' => env('AWS_SECRET_ACCESS_KEY'),
    //         ],
    //     ]);

    //     $createdRecords = array();
    //     $boardTypes = ['HUFD', 'ICUD', 'UICD'];
    //     $selectedBoardTypes = [];etTargetList

    //     foreach ($boardTypes as $boardType) {
    //         $boardTypeKey = "board_type_$boardType";
    //         $s3FileKey = "{$boardType}_s3_file";

    //         // Check if the board type is selected and the corresponding S3 file URL is provided
    //         if (!empty($data[$boardTypeKey]) && !empty($data[$s3FileKey])) {
    //             $selectedBoardTypes[] = $boardType;
    //         }
    //     }


    //     if (count($selectedBoardTypes) > 0) {
    //         $targetList = [];
    //         foreach ($selectedBoardTypes as $boardType) {
    //             $thingTypeName = strtolower($boardType);

    //             $things = $this->listThingsByType($iotClient, $thingTypeName, $data['plantId']);

    //             foreach ($things as $thing) {
    //                 $targetList[] = "arn:aws:iot:eu-central-1:880819439345:thing/{$thing['thingName']}";
    //             }

    //             $filename = $this->extractFileName($data["{$boardType}_s3_file"]);
    //             list($type, $version) = explode('_', $filename);
    //             list($majorVersion, $minorVersion, $pathVersion) = explode('.', $version);

    //             $command = $s3Client->getCommand('GetObject', [
    //                 'Bucket' => 'otabucket001',
    //                 'Key' => $filename,
    //             ]);

    //             $presignedUrl = $this->generatePresignedUrl($s3Client, $command);

    //             try {
    //                 $firmwareUrl = $presignedUrl;

    //                 if ($firmwareUrl === false) {
    //                     throw new Exception("Failed to generate firmware URL.");
    //                 }

    //                 $jobDocument = json_encode([
    //                     "EUDA_BOARD" => $boardType,
    //                     'firmwareUrl' => $firmwareUrl,
    //                     "major_version" => $majorVersion,
    //                     "minor_version" => $minorVersion,
    //                     "patch_version" => $pathVersion,
    //                 ]);

    //                 $result = $this->createJob($iotClient, $data['job_id'], $targetList, $jobDocument);

    //                 $newRecord = new Job();
    //                 $newRecord->fill([
    //                     'job_id' => $result['jobId'],
    //                     'plantId' => $data['plantId'],
    //                     'thing_type' => strtolower($boardType),
    //                     'owner_id' => $data['owner_id'],
    //                     'version_number' => "$majorVersion.$minorVersion.$pathVersion",
    //                 ]);

    //                 $savedRecord = $newRecord->save();

    //                 if ($savedRecord) {
    //                     $createdRecords[] = $savedRecord;
    //                 }
    //             } catch (Exception $e) {
    //                 echo "Error: " . $e->getMessage();
    //             }
    //         }
    //     } else {
    //         Notification::make()
    //         ->title('Error: At least one board type must be selected.')
    //         ->send();
    //         return [];
    //         // Handle the case where no board type is selected
    //         echo "Error: At least one board type must be selected.";
    //     }
    //     //dd($createdRecords);die();
    //     return $createdRecords;
    // }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $iotClient = new IotClient([
                            'version' => 'latest',
                            'region' => env('AWS_DEFAULT_REGION'),
                            'credentials' => [
                                'key' => env('AWS_ACCESS_KEY_ID'),
                                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                            ],
                        ]);

        $s3Client = new S3Client([
                            'version' => 'latest',
                            'region' => env('AWS_DEFAULT_REGION'),
                            'credentials' => [
                                'key' => env('AWS_ACCESS_KEY_ID'),
                                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                            ],
                        ]);
        
        $createdRecords = [];
        $boardTypes = ['HUFD', 'ICUD', 'UICD'];
        $selectedBoardTypes = $this->getSelectedBoardTypes($data, $boardTypes);

        if (empty($selectedBoardTypes)) {
            Notification::make()
                ->title('Error: At least one board type must be selected.')
                ->send();
            return [];
        }

        foreach ($selectedBoardTypes as $boardType) {
            $targetList = $this->getTargetList($iotClient, $boardType, $data['plantId']);
            $firmwareUrl = $this->getFirmwareUrl($s3Client, $data["{$boardType}_s3_file"]);


           // dd($targetList);die();

            if ($firmwareUrl === false) {
                throw new Exception("Failed to generate firmware URL.");
            }

            $jobDocument = $this->getJobDocument($boardType, $firmwareUrl, $data["{$boardType}_s3_file"]);
            $result = $this->createJob($iotClient, $data['job_id'], $targetList, $jobDocument);

            $newRecord = new Job();
            $newRecord->fill([
                'job_id' => $result['jobId'],
                'plantId' => $data['plantId'],
                'thing_type' => strtolower($boardType),
                'owner_id' => $data['owner_id'],
                'version_number' => $this->getVersionNumber($data["{$boardType}_s3_file"]),
            ]);

            if ($newRecord->save()) {
                $createdRecords[] = $newRecord;
            }
        }
        return $createdRecords;
    }

    protected function getSelectedBoardTypes(array $data, array $boardTypes)
    {
        $selectedBoardTypes = [];

        foreach ($boardTypes as $boardType) {
            $boardTypeKey = "board_type_$boardType";
            $s3FileKey = "{$boardType}_s3_file";

            // Check if the board type is selected and the corresponding S3 file URL is provided
            if (!empty($data[$boardTypeKey]) && !empty($data[$s3FileKey])) {
                $selectedBoardTypes[] = $boardType;
            }
        }

        return $selectedBoardTypes;
    }


    protected function getVersionNumber($s3File)
    {
        $filename = $this->extractFileName($s3File);
        list($type, $version) = explode('_', $filename);
        $version = str_replace('.bin', '', $version);
        //dd($version);die();
        return $version;
    }

    protected function getJobDocument($boardType, $firmwareUrl, $s3File)
    {
        $version = $this->getVersionNumber($s3File);
        list($majorVersion, $minorVersion, $patchVersion) = explode('.', $version);

        $jobDocument = json_encode([
            "EUDA_BOARD" => $boardType,
            'firmwareUrl' => $firmwareUrl,
            "major_version" => $majorVersion,
            "minor_version" => $minorVersion,
            "patch_version" => $patchVersion,
        ]);

        return $jobDocument;
    }

    protected function getTargetList($iotClient, $boardType, $plantId)
    {
        $thingTypeName = strtolower($boardType);
        $things = $this->listThingsByType($iotClient, $thingTypeName, $plantId);

        
    
        $targetList = [];
        foreach ($things as $thing) {
            $targetList[] = "arn:aws:iot:eu-central-1:880819439345:thing/{$thing['thingName']}";
        }
    
        return $targetList;
    }

    protected function getFirmwareUrl($s3Client, $s3File)
        {
            $filename = $this->extractFileName($s3File);

            $command = $s3Client->getCommand('GetObject', [
                'Bucket' => 'otabucket001',
                'Key' => $filename,
            ]);

            $request = $s3Client->createPresignedRequest($command, '+1400 minutes');

            // Get the actual presigned-url
            $presignedUrl = (string)$request->getUri();

            return $presignedUrl;
        }

    



    private function listThingsByType($iotClient, $thingTypeName, $plantId)
    {
        
            $things = $iotClient->listThings([
            ]);
           
            // Filter the things based on the plant ID and non-empty attributes.
            $filteredThings = array_filter($things['things'], function ($thing) use ($plantId,$thingTypeName) {
                return isset($thing['attributes']['plantId'])
                    && $thing['attributes']['plantId'] === $plantId
                    && $thing['thingTypeName'] === $thingTypeName
                    && !empty($thing['attributes']); // Ensure the "attributes" array is not empty.
            });
            return $filteredThings;
    }
    private function extractFileName($filePath)
    {
        $parts = explode('/', $filePath);
        return end($parts);
    }

    private function generatePresignedUrl($s3Client, $command)
    {
        $expiresIn = 43200;
        $request = $s3Client->createPresignedRequest($command, "+{$expiresIn} seconds");
        return (string)$request->getUri();
    }

    private function createJob($iotClient, $jobId, $targetList, $jobDocument)
    {
        return $iotClient->createJob([
            'jobId' => Carbon::now()->format('d-m-Y-H-i-s') . '_' . $jobId,
            'targets' => $targetList,
            'document' => $jobDocument,
        ]);
    }

   
    protected function getRedirectUrl():string{
        
        return $this->getResource()::getUrl('index');
    }
}
