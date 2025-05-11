<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Aws\Sqs\SqsClient;
use Cake\Core\Configure;
use Enqueue\Sqs\SqsConnectionFactory;
use Opis\JsonSchema\Validator;

/*
 * Additional bootstrapping and configuration for CLI environments should
 * be put here.
 */

// Set the fullBaseUrl to allow URLs to be generated in shell tasks.
// This is useful when sending email from shells.
//Configure::write('App.fullBaseUrl', php_uname('n'));

// Set logs to different files so they don't have permission conflicts.
if (Configure::check('Log.debug')) {
    Configure::write('Log.debug.file', 'cli-debug');
}
if (Configure::check('Log.error')) {
    Configure::write('Log.error.file', 'cli-error');
}

$client = new SqsClient([
    'region' => env('AWS_REGION', 'eu-west-1'),
    'profile' => 'lbd',
]);
$factory = new SqsConnectionFactory($client);

$context = $factory->createContext();

// Store globally for reuse
Configure::write('Queue.Factory', $factory);
Configure::write('Queue.Context', $context);

$validator = new Validator();

// Register our schema
$validator->resolver()->registerFile(
    'https://greenway.lbdscouts.org.uk/check-in-schema.json',
    'config/schema/check-in-schema.json',
);

Configure::write('Queue.Validator', $validator);
