<?php

namespace Mariojgt\MasterKey\Contracts;

interface MasterKeyHandler
{
    /**
     * Handle MasterKey lifecycle hooks.
     * Suggested hooks:
     *  - before_request_code
     *  - after_web_login
     *
     * Return a Symfony Response or Responsable to short-circuit.
     * Return a string or ['redirect' => string] to override redirect.
     * Otherwise return null.
     */
    /**
     * $context is a typed DTO depending on $hook.
     */
    public function handleMasterKey(string $hook, object $context = null);
}
