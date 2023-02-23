<?php

namespace GenerCode\Commands;

class UploadCommand extends GenericCommand
{
    protected $description = 'Uploads the current directory to reset current files';
    protected $signature = "gc:upload";



    public function zipFiles($zip, $dir, $zip_dir)
    {
        $zip->addEmptyDir($zip_dir);

        if (!file_exists($dir)) return;
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            $filePath = $file->getRealPath();
            $relativePath = ltrim(str_replace("\\", "/", $zip_dir), "/");

            if (!$file->isDir()) {
                $zip->addFile($filePath, $relativePath . "/" . $file->getFilename());
            } else {
                $zip->addEmptyDir($relativePath . "/" . $file->getFilename());
            }
        }
    }

    public function handle()
    {
        try {
            $this->login();
            $zip_name = base_path() . "/src.zip";
       
            $this->info('Zipping files');

            $zip = new \ZipArchive();
            $zip->open($zip_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $this->zipFiles($zip, app_path() . "/Models/", "api/Model");
            $this->zipFiles($zip, app_path() . "/Profile/", "api/Profile");
            $this->zipFiles($zip, app_path() . "/Entity/", "api/Entity");
            $this->zipFiles($zip, app_path() . "/Dictionary/", "api/Dictionary");
            $this->zipFiles($zip, app_path() . "/Repository/", "api/Repository");
            $this->zipFiles($zip, app_path() . "/Resource/", "api/Resource");
            $this->zipFiles($zip, app_path() . "/Validation/", "api/Validation");
            $this->zipFiles($zip, app_path() . "/Http/Controllers/", "api/Controller");
            $this->zipFiles($zip, base_path() . "/database/migrations/", "migrations");
            $zip->addFile(base_path() . "/routes/genercode-api-generate.php", "api/routes.php");
            $zip->addFile(app_path() . "/Providers/GenerCodeAutoServiceProvider.php", "api/serviceprovider.php");
            $zip->addFile(base_path() . "/genercode.json", "genercode.json");

       
            // Zip archive will be created only after closing object
            $zip->close();
            $this->info('Uploading directory');
            $api = new \GenerCodeClient\API($this->http, $this->api_type);
            $res = $api->pushAsset("projects", "src", $this->project_id, $zip_name);
            $this->info($res);
            $this->info("Upload Completed");
            //unlink($zip_name);
        } catch(\GenerCodeClient\ApiErrorException $e) {
            $this->error($e->getMessage());
            return 1;
          //  $output->writeln($e->getDetails());
        } catch (\Exception $e) {
            $this->error($e->getFile() . ": " . $e->getLine() . "\n" . $e->getMessage());
            return 2;
        }
    }
}
