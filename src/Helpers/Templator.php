<?php
namespace Mossengine\Poignant\Helpers;

/**
 * Class Templator
 * @package Mossengine\Poignant\Helpers
 */
class Templator
{
    /**
     * @param $stringTemplate
     * @param $arrayTemplateData
     * @param array $arrayParameters
     * @return mixed
     */
    public static function parse($stringTemplate, $arrayTemplateData, $arrayParameters = []) {
        // Set the initial or continued iteration count for the template
        array_set($arrayParameters, 'iteration', ($intIteration = intval(array_get($arrayParameters, 'iteration', 0)) + 1));

        // If this is the first iteration then inject into the template data some system defaults.
        if (1 === $intIteration) {
            $arrayTemplateData = array_merge(
                $arrayTemplateData,
                [
                    'system' => [
                        'datetime' => [
                            'utc' => ($carbon = \Carbon\Carbon::now('utc'))->toDateTimeString(),
                            'iso8601' => $carbon->toIso8601String(),
                            'unix' => $carbon->timestamp
                        ]
                    ]
                    // Add other default keys with values here
                ]
            );
        }

        // Attempt to find matches to the template pattern
        preg_match_all('/' . array_get($arrayParameters, 'pattern.prefix', '{{') . '(.*?)' . array_get($arrayParameters, 'pattern.suffix', '}}') . '/', $stringTemplate, $arrayMatches);

        // Check we have matches
        if (!empty($arrayMatches[0])) {
            // Define empty search and replace arrays
            $arraySearch = [];
            $arrayReplace = [];

            // Loop over the matches attempting to determine the search and replace
            foreach ($arrayMatches[0] as $index => $match) {
                $key = trim($arrayMatches[1][$index]);
                $value = self::parse(array_get($arrayTemplateData, $key, null), $arrayTemplateData, $arrayParameters);

                // Only if we have a value else nothing to replace
                if (!is_null($value)) {
                    $arraySearch[] = $match;
                    $arrayReplace[] = $value;
                }
            }

            // Replace the searches with the replacements
            $stringTemplate = str_replace($arraySearch, $arrayReplace, $stringTemplate);
        }

        return $stringTemplate;
    }
}