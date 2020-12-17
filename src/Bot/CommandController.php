<?php

namespace Labile\Bot;

trait CommandController
{
    public function commandExecute()
    {
        if (method_exists($this, 'commandList')) {
            $list = $this->commandList();


            foreach ($list as $cmd) {
                if (!is_array($cmd['text'])) {
                    if ($this->formatText($cmd['text'])) {
                        $this->manyMethodsExecute($cmd['method']);
                        break;
                    }
                } else {
                    foreach ($cmd['text'] as $text) {
                        if ($this->formatText($text)) {
                            if (isset($cmd['random_method']) and $cmd['random_method']) {
                                $cmd['method'] = $cmd['method'][array_rand($cmd['method'])];
                            }
                            $this->manyMethodsExecute($cmd['method']);
                            break;
                        }
                    }
                }
            }
        }
    }

    public function eventExecute()
    {
        if (method_exists($this, 'eventList')) {
            $list = $this->eventList();
            $payload = $this->payload;
            $key = key($payload);
            $value = $payload[$key];

            foreach ($list[key($payload)] as $cmd) {
                if ($cmd['key'] == $value) {
                    $this->manyMethodsExecute($cmd['method']);
                    break;
                }
            }
        }
    }

    /**
     * Выполнить несколько методов.
     * @param $methods
     */
    private function manyMethodsExecute($methods)
    {
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $this->$method();
            }
        } else {
            $this->$methods();
        }
    }

    private function formatText(String $text)
    {
        if (mb_substr($text, 0, 1) == '|') {
            $pr = ($this->similar_percent != null) ? $this->similar_percent : 75;;
            return $this->similarTo($text) > $pr / 100;
        }
        if (mb_substr($text, 0, 2) == "[|") {
            return $this->startAs($text);
        }
        if (mb_substr($text, -2, 2) == "|]") {
            return $this->endAs($text);
        }
        if (mb_substr($text, 0, 1) == "{" && mb_substr($text, -1, 1) == "}") {
            return $this->contains($text);
        }
        return $text == $this->text_lower;
    }
}
