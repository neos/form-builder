<?php

namespace Neos\Form\Builder\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\Factory\CommandHookFactoryInterface;
use Neos\ContentRepository\Core\Factory\CommandHooksFactoryDependencies;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;

class UniqueIdentifierCommandHookFactory implements CommandHookFactoryInterface
{
    public function __construct(
        protected ContentRepositoryRegistry $contentRepositoryRegistry
    )
    {

    }

    public function build(CommandHooksFactoryDependencies $commandHooksFactoryDependencies): CommandHookInterface
    {
        return new UniqueIdentifierCommandHook(
            $this->contentRepositoryRegistry,
            $commandHooksFactoryDependencies->contentRepositoryId

        );
    }
}
