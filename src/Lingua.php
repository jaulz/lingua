<?php

namespace Jaulz\Lingua;

use Illuminate\Support\Facades\DB;

class Lingua
{
  public function getSchema()
  {
    return 'lingua';
  }

  public function grant(string $role)
  {
    collect([
      'GRANT USAGE ON SCHEMA %1$s TO %2$s',
      'GRANT SELECT ON TABLE %1$s.definitions TO %2$s',
    ])->each(fn (string $statement) => DB::statement(sprintf($statement, Lingua::getSchema(), $role)));
  }

  public function ungrant(string $role)
  {
  }
}
