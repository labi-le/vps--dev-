<?php

namespace Labile\Bot;

use Conference;
use Timer;

trait ChatEvents
{
    // Действие при приглашении пользователя
    protected function chat_invite_user()
    {
        if ($this->action_member_id == -$this->group_id) {
            $this->vk->msg('Охаё 😡 а ну-ка быстро мне дай админку и зарегистрируй беседу нажав на кнопку ниже или напиши /reg')
                ->kbd($this->vk->buttonText('Кнопка ниже', 'green', ['chat' => 'registration']), true, false)
                ->send();
        }

        $conference = Conference::load($this->chat_id);

        if ($conference->statusWelcomeMsg()) {
            $this->vk->msg($conference->getWelcomeMsg())->send();
        }

        if ($conference->statusTuring_test()) {
            $user_id = $this->action_member_id ?? $this->user_id;
            Timer::load($user_id);

            $turing = $this->turingTest();
            $invalidSum = $turing['array'];
            $result = $turing['result'];
            $text = $turing['text'];


            $this->vk->msg('Тест Дениса Тьюринга на выявление бота или тупого долбаёба, выбери правильный ответ, иначе  ~ты|' . $user_id . '~ будешь кикнут' . \PHP_EOL . \PHP_EOL . 'Пример таков: ' . $text)
                ->kbd(call_user_func(function () use ($invalidSum) {
                    $kb = [];
                    $str = 0;
                    foreach ($invalidSum as $sum) {
                        $kb[$str][] = $this->vk->buttonText($sum, 'white', ['chat' =>  'push_button', 'sum' => $sum]);
                    }
                    return $kb;
                }), true, false)
                ->send();

            $timer = Timer::load($user_id);
            $timer->set_valid_num($result);
            $timer->off_test_passed();

            sleep(60);


            if ($timer->test_passed() == 'off') {
                $this->vk->removeChatUser($this->chat_id, $user_id);
            }

            $timer->remove();
        }
    }

    // Действие при приглашении пользователя по ссылке
    protected function chat_invite_user_by_link()
    {
        $this->chat_invite_user();
    }

    // Действие при выходе юзера из беседы\кик
    protected function chat_kick_user()
    {
        $status = Conference::load($this->chat_id);

        if ($status->allStatus()['auto_kick_status']) {
            $this->vk->removeChatUser($this->chat_id, $this->action_member_id);
        }

        if ($status->allStatus()['exit_msg_status']) {
            $this->vk->msg($status->getExitMsg())->send();
        }
    }


    // Действие при обновлении фотки беседы
    protected function chat_photo_update()
    {
    }

    // Действие при удалении фото беседы
    protected function chat_photo_remove()
    {
    }

    // Действие при закреплении сообщения
    protected function chat_pin_message()
    {
    }

    // Действие при откреплении
    protected function chat_unpin_message()
    {
    }
}
