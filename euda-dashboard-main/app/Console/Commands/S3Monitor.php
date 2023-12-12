<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Cache;
use Aws\Iot\IotClient;
use Illuminate\Http\Request;
use PhpMqtt\Client\MQTTClient;
use Aws\IotJobsDataPlane\IotJobsDataPlaneClient;


class S3Monitor extends Command
{
    protected $signature = 'app:s3-monitor';
    protected $description = 'Monitor S3 bucket and IoT jobs';

    public function handle()
    {
        $s3 = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucketName = 'otabucket001';

        try {
            $objects = $s3->listObjectsV2([
                'Bucket' => $bucketName,
            ]);

            $isNewFileUploaded = false;
            $latestVersion = null;
            $latestUploadDate = null;
           

            if (!empty($objects['Contents'])) {

                usort($objects['Contents'], function ($a, $b) {
                    return strtotime($b['LastModified']) - strtotime($a['LastModified']);
                });
                $count = 0;
                $latestUploads = [];
                foreach ($objects['Contents'] as $object) {
                    $this->info('Found new file: ' . $object['Key']);
                    $fileExtension = pathinfo($object['Key'], PATHINFO_EXTENSION);
                
                    if ($fileExtension === 'bin') {
                        $thingType = $this->getThingTypeFromFileName($object['Key']);
                        $version = $this->getVersionFromFileName($object['Key']);
                
                        // Check if the thing_type already exists in the array and if the version is newer
                        if (!isset($latestVersions[$thingType]) || version_compare($version, $latestVersions[$thingType]['version'], '>')) {
                            $latestVersions[$thingType] = [
                                'version' => $version,
                                'thing_type' => $thingType,
                            ];
                        }
                
                        $count++;
                
                        if ($count >= 5) {
                            break;
                        }
                    }
                }
                $latestUploads = array_values($latestVersions);

                Cache::forever('latestUploads', $latestUploads);
            } else {
                $this->info('No files found in the bucket.');
                Cache::forever('latestUploads', []); // Set an empty array if no files are found
            }
        } catch (\Exception $e) {
            $this->error('Error while monitoring S3 bucket: ' . $e->getMessage());
        }

        // $client = new IotClient([
        //     'region' => env('AWS_DEFAULT_REGION'),
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key' => env('AWS_ACCESS_KEY_ID'),
        //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     ],
        // ]);


        // try {
        //     $jobs = $client->listJobs([]);
        //     $jobStatuses = [];
        
        //     foreach ($jobs['jobs'] as $job) {
        //         $jobId = $job['jobId'];
        
        //         try {
        //             $result = $client->describeJob([
        //                 'jobId' => $jobId,
        //             ]);
        //             $testResult = $client->getJobDocument(['jobId' => $jobId]);
        
        //             $jobStatus = $result['job']['status'];
        //             $thingType = null;
        //             $version = null;
        
        //             if (isset($testResult['document'])) {
        //                 $decodedData = json_decode($testResult['document'], true);
        //                 $thingType = $decodedData['EUDA_BOARD'];
        //                 $version = $decodedData['major_version'] . '.' . $decodedData['minor_version'] . '.' . $decodedData['patch_version'];
        //             }
        
        //         } catch (\Exception $e) {
        //             $jobStatus = 'Error';
        //         }
        
        //         $jobStatuses[$jobId] = $jobStatus;
        
        //         $existingJob = \DB::table('jobs')
        //             ->where('job_id', $jobId)
        //             ->first();
        
        //         if ($existingJob) {
        //             \DB::table('jobs')
        //                 ->where('job_id', $jobId)
        //                 ->update([
        //                     'status' => $jobStatus
        //                 ]);
        //         } 
        //     }
        // } catch (\Exception $e) {
        //     $this->error('Error while monitoring IoT jobs: ' . $e->getMessage());
        // }
        

           
    }

    private function getThingTypeFromFileName($fileName)
{
    $parts = explode('_', $fileName);
    $thingType = $parts[0]; // Assuming the thing_type is the first part before "_"
    
    return strtolower($thingType); // Convert to lowercase
}

private function getVersionFromFileName($fileName)
{
    $parts = explode('_', $fileName);
    $version = isset($parts[1]) ? $parts[1] : null;
    
    // Remove the ".bin" suffix from the version, if present
    if ($version !== null) {
        $version = preg_replace('/\.bin$/', '', $version);
    }
    
    return $version;
}

}
