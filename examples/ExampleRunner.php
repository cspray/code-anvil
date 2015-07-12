<?php

declare(strict_types = 1);

use CodeAnvil\Info\Info;

/**
 * @license See LICENSE file in project root
 */
class ExampleRunner {

    const OPT_DUMP_GENERATED_CODE = 'opt.dump_generated_code';
    const OPT_PROMPT_STRICT_TYPES = 'opt.prompt_strict_types';

    private static $options = [
        self::OPT_DUMP_GENERATED_CODE => true,
        self::OPT_PROMPT_STRICT_TYPES => true
    ];

    public static function setOptionDumpGeneratedCode(bool $doDump) {
        self::$options[self::OPT_DUMP_GENERATED_CODE] = $doDump;
    }

    public static function setOptionPromptStrictTypes(bool $doPrompt) {
        self::$options[self::OPT_PROMPT_STRICT_TYPES] = $doPrompt;
    }

    public static function generateAndRequireCode(Info $info) {
        if (self::OPT_PROMPT_STRICT_TYPES) {
            $doStrictTypes = ExampleRunner::getUserInput('Do you want strict types? (y/n) ');
            if (strtolower($doStrictTypes) === 'y') {
                $info->declareStrict();
            }
        }


        $code = self::getGenerator()->generate($info);
        $path = tempnam(sys_get_temp_dir(), 'code_generator_exmaple');
        $h = fopen($path, 'w');
        fwrite($h, $code);
        fclose($h);

        if (self::$options[self::OPT_DUMP_GENERATED_CODE]) {
            self::stdout('Generated code');
            self::stdout(str_repeat('=', 80));
            self::stdout('');
            self::stdout($code);
        }

        require_once $path;
    }

    public static function getUserInput(string $prompt) : string {
        self::stdout($prompt, false);
        $h = fopen('php://stdin', 'r');
        return trim(fgets($h), "\n");
    }

    private static function getGenerator() {
        static $instance;

        if (!$instance) {
            $instance = new \CodeAnvil\CodeGenerator();
        }

        return $instance;
    }

    private static function stdout(string $msg, bool $appendNewLine = true) {
        if ($appendNewLine) {
            $msg .= PHP_EOL;
        }

        echo $msg;
    }

}