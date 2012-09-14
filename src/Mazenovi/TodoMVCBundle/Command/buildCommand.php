<?php

namespace Mazenovi\TodoMVCBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use FOS\UserBundle\Util\UserManipulator;

use Mazenovi\TodoMVCBundle\Model\Todo;
use Mazenovi\TodoMVCBundle\Model\TodoQuery;

use FOS\UserBundle\Propel\User;
use FOS\UserBundle\Propel\UserQuery;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class buildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('todomvc:build')
            ->setDescription('build and load data for MazenoviTodoMVCBundle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->preCommands($input, $output);

        if (!$this->getContainer()->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
			return;
		}

        $todos = TodoQuery::create()->find();        

        foreach($todos AS $todo)
        {

        	// creating the ACL
            $aclProvider = $this->getContainer()->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($todo);
            $acl = $aclProvider->createAcl($objectIdentity);

            // retrieving the security identity of the currently logged-in user
            $securityContext = $this->getContainer()->get('security.context');
            $user = UserQuery::create()->filterById($todo->getFosUserId())->findOne();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);
        }
       
        $output->writeln('<comment>Todos</comment> are <info>ready</info>');
    }
    
    private function preCommands(InputInterface $input, OutputInterface $output)
    {

        $command = $this->getApplication()->find('propel:build');
        $arguments = array(
            'command' => 'propel:build',
            '--insert-sql'  => true,
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        $command = $this->getApplication()->find('propel:fixtures:load');
        $arguments = array(
            'command' => 'propel:fixtures:load',
            'bundle'  => '@MazenoviTodoMVCBundle',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        /*
        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'todomvc',
            'email' => 'todomvc@m4z3.me',
            'password' => 'todomvc',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        $command = $this->getApplication()->find('fos:user:promote');
        $arguments = array(
            'command' => 'fos:user:promote',
            'username' => 'todomvc',
            'role' => 'ROLE_ADMIN',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'todomvcuser',
            'email' => 'todomvcuser@m4z3.me',
            'password' => 'todomvcuser',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        */
    }
    
}