<?php
namespace JsLocalization;

class JsLocalizationHelper
{
    
    /**
     * Array of message keys. A set of messages that are
     * supposed to be exported to the JS code in addition
     * to Config::get('js-localization::config.messages').
     *
     * @var array
     */
    protected $messagesToExport = array();

    /**
     * Allows registration of additional messages to
     * export to the JS code. The additional messages
     * registered using this method extend the
     * Config::get('js-localization::config.messages')
     * array.
     * Don't forget to run `php artisan js-localization:refresh`!
     *
     * @param array $messageKeys    Array of message keys.
     * @return void
     */
    public function addMessagesToExport (array $messageKeys)
    {
        $messagesToExport = $this->messagesToExport;

        foreach ($messageKeys as $index=>$key) {
            $this->resolveMessageKey($key, $index, function($qualifiedKey) use(&$messagesToExport)
                {
                    $messagesToExport[$qualifiedKey] = Lang::get($qualifiedKey);
                });
        }

        $this->messagesToExport = array_unique($messagesToExport);
    }

    /**
     * Returns the message keys previously registered
     * by addMessagesToExport(). Nested arrays have
     * already been resolved to a single flat array.
     *
     * @return array
     *      Array of message keys to export to the JS code.
     */
    public function getAdditionalMessages ()
    {
        return $this->messagesToExport;
    }

    /**
     * Returns the concatenation of prefix and key if the key
     * is a string. If the key is an array then the function
     * will recurse.
     *
     * @param mixed $key            An array item read from the configuration ('messages' array).
     * @param mixed $keyIndex       The array index of $key. Is neccessary if $key is an array.
     * @param callable $callback    A callback function: function($fullyQualifiedKey).
     * @param string $prefix        Optional key prefix.
     */
    public function resolveMessageKey ($key, $keyIndex, $callback, $prefix="")
    {
        if (is_array($key)) {
            $_prefix = $prefix ? $prefix.$keyIndex."." : $keyIndex.".";

            foreach ($key as $_index=>$_key) {
                $this->resolveMessageKey($_key, $_index, $callback, $_prefix);
            }

        } else {
            $callback($prefix.$key);
        }
    }

}