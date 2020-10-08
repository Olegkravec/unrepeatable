# Unrepeatable - repeated command execution preven

## Installing 

`composer require olegkravec/unrepeatable "^1"`

- Also if you need you can add file `instances.log` in .gitignore
- Also if there is Laravel project you can set env `PROVIDER_STATES_FILE`, that will change name of instance log file. 


## Intro

`Unrepeatable` - must be required in your project if you need control/prevent multiple runs of your functions.
For example, you have docker where you need for every `git push` make `php artisan db:seed` - but in this case each of seeders will be executed... Unrepeatable is for preventing this cases, library check if traited class was already runned, and say you about this.


## Using

### Case #1 - IUnrepeatable

```
class GenerateDefaultUsersSeeder extends Seeder
{
    use IUnrepeatable; // Just require me
    
    public function run()
    {
        User::factory()->count(10)->create();
    }
}
```

In this case `GenerateDefaultUsersSeeder` will be able to run factory only one time. If You will run the seeder again, you will see in your console:
```
Seeding: Database\Seeders\GenerateDefaultUsersSeeder

Already invoked! Skipping...
Seeded:  Database\Seeders\GenerateDefaultUsersSeeder (0.76ms)
```

### Case #2 - Unrepeatable

```
class GenerateDefaultUsersSeeder extends Seeder
{
    use Unrepeatable; // Require me

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!$this->isPreviouslyRunned()) // Check where are you need if this class was already runned
            User::factory()->count(10)->create();
    }
}
```

In this case you can check previous runs in the place you need. 

Also you can specify key of sensitive functionality for checks, for example:

```
public function run()
    {
        if(!$this->isPreviouslyRunned()) // default check
            User::factory()->count(1000)->create();
            
        if(!$this->isPreviouslyRunned("10k-clients"))
            Clients::factory()->count(10000)->create();
            
        if(!$this->isPreviouslyRunned("100k-of-another-models"))
            AnotherModel::factory()->count(100000)->create();
    }
    
```

In this case will be created 3 checkpoints in your class with different keywords.
