<?php

namespace Jaulz\Lingua;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Jaulz\Lingua\Facades\Lingua;

class LinguaServiceProvider extends PackageServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->extendBlueprint();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('lingua')
            ->hasConfigFile('lingua')
            ->hasMigration('create_lingua_extension')
            ->hasMigration('grant_usage_on_lingua_extension')
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->publishConfigFile()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('jaulz/lingua');
            });
    }

    public function extendBlueprint()
    {
      Blueprint::macro('lingua', function (
        array $sourceName,
        string $targetName = 'language_code',
        array $options = [],
      ) {
        /** @var \Illuminate\Database\Schema\Blueprint $this */
        $prefix = $this->prefix;
        $tableName = $this->table;
        $schema = config('lingua.schema') ?? 'public';
  
        $command = $this->addCommand(
          'lingua',
          compact(
            'schema',
            'prefix',
            'tableName',
            'sourceName',
            'targetName',
            'options'
          )
        );
      });
  
      PostgresGrammar::macro('compileLingua', function (
        Blueprint $blueprint,
        Fluent $command
      ) {
        /** @var \Illuminate\Database\Schema\Grammars\PostgresGrammar $this */
        $schema = $command->schema;
        $prefix = $command->prefix;
        $tableName = $command->tableName;
        $sourceName = $command->sourceName;
        $targetName = $command->targetName;

        return [
          sprintf(
            <<<SQL
              SELECT 
                %s.create(
                  %s, 
                  %s, 
                  %s, 
                  %s.jsonb_array_to_text_array(%s::jsonb)
                );
            SQL
            ,
            Lingua::getSchema(),
            $this->quoteString($schema),
            $this->quoteString($prefix . $tableName),
            $this->quoteString($targetName),
            Lingua::getSchema(),
            $this->quoteString(json_encode($sourceName)),
          ),
        ];
      });
    }
}