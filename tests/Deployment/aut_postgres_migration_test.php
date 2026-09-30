<?php
$root=dirname(__DIR__,2);
require getenv('FIT_TEST_BOOTSTRAP') ?: $root.'/vendor/autoload.php';
$appRoot=getenv('FIT_TEST_APP_ROOT') ?: $root;
$app=require $appRoot.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Database\Schema\Blueprint;
config()->set('database.connections.aut_pg_fixture',[
 'driver'=>'pgsql','host'=>getenv('AUT_TEST_SOCKET'),'port'=>55487,
 'database'=>'postgres','username'=>getenv('AUT_TEST_USER'),'password'=>'','charset'=>'utf8','schema'=>'public','sslmode'=>'disable',
]);
DB::setDefaultConnection('aut_pg_fixture');
foreach(['users','players','athletes','health_records'] as $table){
 Schema::create($table,fn(Blueprint $t)=>$t->id());
 DB::table($table)->insert(['id'=>1]);
}
$old=require $root.'/database/migrations/2024_01_15_000006_create_tue_requests_table.php';
$old->up();
DB::table('tue_requests')->insert(['athlete_id'=>1,'medication'=>'Fixture','reason'=>'Fixture',
 'physician_id'=>1,'request_date'=>'2026-10-01']);
$migration=require $root.'/database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php';
$types=DB::select("SELECT column_name,data_type FROM information_schema.columns WHERE table_name='tue_requests' AND column_name IN ('athlete_id','medication','reason') ORDER BY column_name");
$locker=new PDO('pgsql:host='.getenv('AUT_TEST_SOCKET').';port=55487;dbname=postgres',getenv('AUT_TEST_USER'),'');
$locker->beginTransaction();$locker->exec('LOCK TABLE health_records IN ACCESS EXCLUSIVE MODE');
DB::beginTransaction();$timedOut=false;$start=microtime(true);
try{$migration->up();}catch(Illuminate\Database\QueryException $e){$timedOut=($e->errorInfo[0]??null)==='55P03';}
DB::rollBack();$locker->rollBack();
if(!$timedOut||microtime(true)-$start>14)throw new RuntimeException('Lock wait was not bounded');
echo "PASS PostgreSQL lock timeout; transaction rolled back\n";
DB::transaction(fn()=>$migration->up());
$after=DB::select("SELECT column_name,data_type FROM information_schema.columns WHERE table_name='tue_requests' AND column_name IN ('athlete_id','medication','reason') ORDER BY column_name");
if(json_encode($types)!==json_encode($after))throw new RuntimeException('Historic types changed');
if(DB::table('tue_requests')->where('medication','Fixture')->count()!==1)throw new RuntimeException('Historic row lost');
DB::table('tue_requests')->insert(['player_id'=>1,'health_record_id'=>1,'physician_id'=>1,'request_date'=>'2026-10-01']);
DB::transaction(fn()=>$migration->up());
if(DB::table('tue_requests')->count()!==2)throw new RuntimeException('Retry lost data');
echo "PASS PostgreSQL migration, nullable fields, unchanged types and safe retry\n";
