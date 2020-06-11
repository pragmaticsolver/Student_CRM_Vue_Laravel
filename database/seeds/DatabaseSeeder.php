<?php

use App\CustomSeederLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $output = new Symfony\Component\Console\Output\ConsoleOutput();

        $file_paths = glob(base_path()."/database/seeds/custom/*.php");

        $output->writeln("<info>Found ". count($file_paths). " seeder file(s)</info>");

        try {
            foreach($file_paths as $file_path)
            {
                $seederName = explode('.',basename($file_path))[0];

                $executed = CustomSeederLog::where('seeder', $seederName)->exists();
                
                $output->writeln("<info>". $seederName." - ". ( $executed ? "Already Executed" : "Executing" ) ."</info>");

                if(!$executed) 
                {
                    DB::beginTransaction();

                    $this->call('Database\Seeds\Custom\\' . $seederName);
                    $customSeederLog = new CustomSeederLog();
                    $customSeederLog->seeder = $seederName;
                    $customSeederLog->executed_at = Carbon::now()->format('Y-m-d H:i:s');
                    $customSeederLog->save();
                    
                    DB::commit();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
