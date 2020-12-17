<?php

namespace Labile\Bot;

trait Utils
{

    public function translit($str)
    {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "'", "э" => "e", "ю" => "yu", "я" => "ya"
        );
        return strtr($str, $tr);
    }

    /**
     * Похоже на.
     * @param string $text
     * @return float|int
     */
    public function similarTo(string $text)
    {
        $textFromBot = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $this->text_lower);
        $text = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $text);

        $length = strlen($textFromBot);
        $lengthB = strlen($text);

        $i = 0;
        $segmentCount = 0;
        $segmentsInfo = array();
        $segment = '';
        while ($i < $length) {
            $char = mb_substr($text, $i, 1);
            if (mb_strpos($textFromBot, $char) !== FALSE) {
                $segment = $segment . $char;
                if (mb_strpos($textFromBot, $segment) !== FALSE) {
                    $segmentPosA = $i - mb_strlen($segment) + 1;
                    $segmentPosB = mb_strpos($textFromBot, $segment);
                    $positionDiff = abs($segmentPosA - $segmentPosB);
                    $posFactor = ($length - $positionDiff) / $lengthB;
                    $lengthFactor = mb_strlen($segment) / $length;
                    $segmentsInfo[$segmentCount] = array('segment' => $segment, 'score' => ($posFactor * $lengthFactor));
                } else {
                    $segment = '';
                    $i--;
                    $segmentCount++;
                }
            } else {
                $segment = '';
                $segmentCount++;
            }
            $i++;
        }

        $totalScore = array_sum(array_map(function ($v) {
            return $v['score'];
        }, $segmentsInfo));
        return $totalScore;
    }

    /**
     * Начинается с
     * @param string $text
     * @return bool
     */
    public function startAs(string $text)
    {
        $textFromBot = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $this->text_lower);
        $text1 = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $text);

        $firstWord = explode(' ', $text1)[0];
        $firstWordFromBot = explode(' ', $textFromBot)[0];

        return mb_substr($firstWord, 1) == $firstWordFromBot;
    }

    /**
     * Заканчивается на
     * @param string $text
     * @return bool
     */
    public function endAs(string $text)
    {
        $textFromBot = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $this->text_lower);
        $text1 = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $text);

        $firstWord = explode(' ', $text1);
        $firstWordFromBot = explode(' ', $textFromBot);

        return mb_substr($firstWord[count($firstWord) - 1], 0, -1) == $firstWordFromBot[count($firstWordFromBot) - 1];
    }

    /**
     * Содержит
     * @param string $text
     * @return bool
     */
    public function contains(string $text)
    {
        $textFromBot = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $this->text_lower);
        $text1 = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $text);

        return mb_stripos($textFromBot, $text1) !== false;
    }

    /**
     * Загрузить информацию о чате.
     * @param int $chat_id
     */
    public function loadChatMembers(int $chat_id): void
    {
        $members = $this->vk->getConversationMembers($chat_id);

        $admin_items = array_filter($members['items'], function ($item) {
            return !empty($item['is_admin']);
        });
        $admins_ids = array_map(
            function ($item) {
                return $item['member_id'];
            },
            $admin_items
        );
        $count = 0;
        $admins = [
            'items' => $admin_items,
        ];
        if (!empty($members['profiles'])) {
            foreach ($members['profiles'] as $group) {
                if (in_array($group['id'], $admins_ids)) {
                    $count++;
                    $admins['profiles'][] = $group;
                }
            }
        }
        if (!empty($members['groups'])) {
            foreach ($members['groups'] as $group) {
                if (in_array(-$group['id'], $admins_ids)) {
                    $count++;
                    $admins['groups'][] = $group;
                }
            }
        }
        $admins['count'] = $count;
        $members_ids = array_map(
            function ($item) {
                return $item['member_id'];
            },
            $members['items']
        );

        $this->members = $members;
        $this->members_ids = $members_ids;
        $this->admins = $admins;
        $this->admins_ids = $admins_ids;
    }

    public function textWithoutPrefix()
    {
        return strstr($this->text, " ");
    }

    public function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }
}
