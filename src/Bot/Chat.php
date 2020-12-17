<?php

namespace Labile\Bot;

trait Chat
{
    /**
     * Получить список участников беседы.
     * @return array
     */
    public function getChatMembers(): array
    {
        return $this->members;
    }

    /**
     * Получить список id-ов участников беседы.
     * @return array
     */
    public function getChatMembersIds(): array
    {
        return $this->members_ids;
    }

    /**
     * Получить список администраторов беседы.
     * @return array
     */
    public function getChatAdmins(): array
    {
        return $this->admins;
    }

    /**
     * Получить список id-ов администраторов беседы.
     * @return array
     */
    public function getChatAdminsIds(): array
    {
        return $this->admins_ids;
    }
}
