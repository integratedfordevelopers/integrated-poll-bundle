<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class PollRepository extends DocumentRepository
{
    /**
     * @return Poll
     */
    public function findLatestPoll()
    {
        return $this->createQueryBuilder()
            ->sort('createdAt', 'DESC')
            ->getQuery()
            ->getSingleResult();
    }
}
