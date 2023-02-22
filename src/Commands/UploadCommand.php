<?php

namespace GenerCode\Commands;

class UploadCommand extends GenericCommand
{
    protected $description = 'Uploads the current directory to reset current files';
    protected $signature = "gc:upload";



    public function zipFiles($zip, $dir)
    {
        $zip->addEmptyDir($dir);

        if (!file_exists($this->download_dir . "/" . $dir)) return;
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->download_dir . "/" . $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($this->download_dir));
            $relativePath = ltrim(str_replace("\\", "/", $relativePath), "/");

            if (!$file->isDir()) {
                $zip->addFile($filePath, $relativePath);
            } else {
                $zip->addEmptyDir($relativePath);
            }
        }
    }

    public function handle()
    {
        try {
            $this->login();
            $zip_name = $this->download_dir . "/project.zip";
       
            $this->info('Zipping files');

            $zip = new \ZipArchive();
            $zip->open($zip_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $this->zipFiles($zip, app_path() . "/Models/", "api/Model");
            $this->zipFiles($zip, app_path() . "/Entity/", "api/Entity");
            $this->zipFiles($zip, app_path() . "/Dictionary/", "api/Dictionary");
            $this->zipFiles($zip, app_path() . "/Repository/", "api/Repository");
            $this->zipFiles($zip, app_path() . "/Resource/", "api/Resource");
            $this->zipFiles($zip, app_path() . "/Validation/", "api/Validation");
            $this->zipFiles($zip, app_path() . "/Http/Controllers/", "api/Controller");
            $this->zipFiles($zip, base_path() . "/database/migrations/", "migrations");
            $this->zipFiles($zip, base_path() . "/genercode.json", "genercode.json");

       
            // Zip archive will be created only after closing object
            $zip->close();
            $this->info('Uploading directory');
            $this->info($this->http->pushAsset("/projects/src/" . $this->project_id, "src", $zip_name));
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
