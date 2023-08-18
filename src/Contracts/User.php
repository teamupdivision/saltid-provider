<?php

namespace Teamupdivision\SaltId\Contracts;

interface User
{
    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the full name of the user.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get the e-mail address of the user.
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string|null
     */
    public function getAvatar();

    /**
     * Get the role of the user.
     *
     * @return string|null
     */
    public function getRole();


    /**
     * Get the company of the user.
     *
     * @return string|null
     */
    public function getCompany();
}
