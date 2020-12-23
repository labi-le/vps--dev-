<?php http_response_code(404);exit("404");?>
LOGS:

[Exception] 19.12.20 14:09:23
CODE: 77777
MESSAGE: Запрос к вк вернул пустоту. Завершение 5 попыток отправки

                                  Метод:https://lp.vk.com/wh196756261?
Параметры:
{"act":"a_check","key":"bd3336dc7d883f3a50f1e648f87eff6c3f25d95e","ts":"45698","wait":25}
in: /home/labile/Рабочий стол/vps [dev]/src/simplevk/src/LongPoll.php:132
Stack trace:
#0 /home/labile/Рабочий стол/vps [dev]/src/simplevk/src/LongPoll.php(99): DigitalStars\SimpleVK\LongPoll->getData()
#1 /home/labile/Рабочий стол/vps [dev]/src/simplevk/src/LongPoll.php(49): DigitalStars\SimpleVK\LongPoll->processingData()
#2 /home/labile/Рабочий стол/vps [dev]/long.php(29): DigitalStars\SimpleVK\LongPoll->listen()
#3 {main}

