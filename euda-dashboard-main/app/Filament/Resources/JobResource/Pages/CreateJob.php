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

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;
    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin || Auth::user()->is_technician;
    }
    
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
        $selectedBoardTypes = [];

        foreach ($boardTypes as $boardType) {
            $boardTypeKey = "board_type_$boardType";
            $s3FileKey = "{$boardType}_s3_file";

            // Check if the board type is selected and the corresponding S3 file URL is provided
            if (!empty($data[$boardTypeKey]) && !empty($data[$s3FileKey])) {
                $selectedBoardTypes[] = $boardType;
            }
        }

        if (count($selectedBoardTypes) > 0) {
            $targetList = [];

            foreach ($selectedBoardTypes as $boardType) {
                $thingTypeName = strtolower($boardType);

                $things = $this->listThingsByType($iotClient, $thingTypeName, $data['plantId']);

                foreach ($things as $thing) {
                    $targetList[] = "arn:aws:iot:eu-central-1:880819439345:thing/{$thing['thingName']}";
                }

                $filename = $this->extractFileName($data["{$boardType}_s3_file"]);
                list($type, $version) = explode('_', $filename);
                list($majorVersion, $minorVersion, $pathVersion) = explode('.', $version);

                $command = $s3Client->getCommand('GetObject', [
                    'Bucket' => 'otabucket001',
                    'Key' => $filename,
                ]);

                $presignedUrl = $this->generatePresignedUrl($s3Client, $command);

                try {
                    $firmwareUrl = $presignedUrl;

                    if ($firmwareUrl === false) {
                        throw new Exception("Failed to generate firmware URL.");
                    }

                    $jobDocument = json_encode([
                        "EUDA_BOARD" => $boardType,
                        'firmwareUrl' => $firmwareUrl,
                        "major_version" => $majorVersion,
                        "minor_version" => $minorVersion,
                        "patch_version" => $pathVersion,
                    ]);

                    $result = $this->createJob($iotClient, $data['job_id'], $targetList, $jobDocument);

                    $newRecord = new Job();
                    $newRecord->fill([
                        'job_id' => $result['jobId'],
                        'plantId' => $data['plantId'],
                        'thing_type' => strtolower($boardType),
                        'owner_id' => $data['owner_id'],
                        'version_number' => "$majorVersion.$minorVersion.$pathVersion",
                    ]);

                    $savedRecord = $newRecord->save();

                    if ($savedRecord) {
                        $createdRecords[] = $savedRecord;
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        } else {
            // Handle the case where no board type is selected
            echo "Error: At least one board type must be selected.";
        }
        //dd($createdRecords, $data);die();
        return $createdRecords;
    }



    private function listThingsByType($iotClient, $thingTypeName, $plantId)
    {
        $things = $iotClient->listThings([
            'thingTypeName' => $thingTypeName,
        ]);
    
        $filteredThings = array_filter($things['things'], function ($thing) use ($plantId) {
            return isset($thing['attributes']['plantId'])
                && $thing['attributes']['plantId'] === $plantId
                && !empty($thing['attributes']); // Check if the "attributes" array is not empty
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
