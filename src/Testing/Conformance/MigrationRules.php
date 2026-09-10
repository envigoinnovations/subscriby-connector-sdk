<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Conformance;

/**
 * The data-ownership rules a connector's migrations must keep.
 *
 * A connector creates only tables prefixed with its key and never alters a
 * core table; the core, in turn, never holds a foreign key into a connector's
 * table. Read from the migration sources rather than the database so the rule
 * fails on the developer's machine before anything runs.
 */
final class MigrationRules
{
    /**
     * @param   string        $connector       The connector key, which is also the table prefix.
     * @param   string        $migrationsPath  The package's `database/migrations` directory.
     * @return  list<string>  Every offence, as `file: what`.
     */
    public static function violations(string $connector, string $migrationsPath): array
    {
        if (! is_dir($migrationsPath)) {
            return [];
        }

        $prefix = $connector.'_';
        $offences = [];

        foreach (glob(rtrim($migrationsPath, '/\\').'/*.php') ?: [] as $file) {
            $source = (string) file_get_contents($file);
            $name = basename($file);

            preg_match_all('/Schema::create\(\s*[\'"]([^\'"]+)[\'"]/', $source, $created);
            preg_match_all('/Schema::(?:table|drop|dropIfExists|rename)\(\s*[\'"]([^\'"]+)[\'"]/', $source, $touched);

            foreach ($created[1] as $table) {
                if (! str_starts_with($table, $prefix)) {
                    $offences[] = sprintf('%s: creates "%s" without the %s prefix', $name, $table, $prefix);
                }
            }

            foreach ($touched[1] as $table) {
                if (! str_starts_with($table, $prefix)) {
                    $offences[] = sprintf('%s: alters "%s", which is not the connector\'s table', $name, $table);
                }
            }
        }

        return $offences;
    }
}
