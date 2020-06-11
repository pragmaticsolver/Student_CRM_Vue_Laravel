<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateCustomSeederFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom_seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom seeder class';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $folder = 'database/seeds/custom';

        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $seeder_name = "Seeder".time();
        $seeder_file = $folder .'/'. $seeder_name .'.php';

$content = "<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;

class ".$seeder_name." extends Seeder
{
    public function run()
    {
        
    }
}";

        file_put_contents($seeder_file, $content);
        exec('composer dump-autoload');
        dump("New custom seeder file generated: " . $seeder_file);
    }
}
