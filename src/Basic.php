<?php

namespace Uauth;

class Basic
{
    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $realm;

    /**
     * @var callable
     */
    protected $verify;

    /**
     * @var callable
     */
    protected $deny;

    /**
     * Constructor.
     */
    public function __construct($realm = "Secured Area", array $allowedUsers = array())
    {
        $this->realm  = $realm;
        $this->verify = function ($user, $password) use ($allowedUsers) {
            return isset($allowedUsers[$user]) && $allowedUsers[$user] == $password;
        };
    }

    /**
     * Set realm
     * @param  string $realm
     *
     * @return Basic The current instance
     */
    public function realm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Set the user verification system
     *
     * @param callable $verify
     *
     * @return Basic
     */
    public function verify(callable $verify)
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Set the callable executed on deny by the verification
     *
     * @param callable $deny
     *
     * @return Basic
     */
    public function deny(callable $deny)
    {
        $this->deny = $deny;

        return $this;
    }

    /**
     * Process the basic auth
     *
     * @return Basic
     */
    public function auth()
    {
        $user     = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

        if (is_null($user) || !(bool) call_user_func($this->verify, $user, $password)) {
            header(sprintf('WWW-Authenticate: Basic realm="%s"', $this->realm));
            header('HTTP/1.0 401 Unauthorized');

            if ($this->deny) {
                call_user_func($this->deny, $user);
            }

            exit;
        }

        $this->user     = $user;
        $this->password = $password;

        return $this;
    }

    /**
     * Get the verified user
     *
     * Only available after the auth method
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the password of the verified user
     *
     * Only available after the auth method
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
