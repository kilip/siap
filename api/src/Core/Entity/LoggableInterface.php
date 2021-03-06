<?php

/*
 * This file is part of the Sistim Informasi Antar Paroki (SIAP) project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SIAP\Core\Entity;

use SIAP\User\Entity\User;

interface LoggableInterface
{
    /**
     * @return static
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt);

    public function getUpdatedAt(): ?\DateTimeInterface;

    /**
     * @return static
     */
    public function setUpdatedBy(?User $user);

    public function getUpdatedBy(): ?User;
}
