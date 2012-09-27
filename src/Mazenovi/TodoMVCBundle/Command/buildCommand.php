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
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
//use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

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

        $this->aclCommands($input, $output);
       
        $output->writeln('<comment>Todos</comment> are <info>ready</info>');
    }

    private function aclCommands(InputInterface $input, OutputInterface $output)
    {
        /*
        if (!$this->getContainer()->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
            return;
        }
        */

        $todos = TodoQuery::create()->find();        

        $aclProvider = $this->getContainer()->get('security.acl.provider');

        // every 
        foreach($todos AS $todo)
        {
            // creating the ACL
            $objectIdentity = ObjectIdentity::fromDomainObject($todo);
            $acl = $aclProvider->createAcl($objectIdentity);
            $securityContext = $this->getContainer()->get('security.context');
            $user = UserQuery::create()->filterById($todo->getFosUserId())->findOne();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);
        }

        // todoadmin owns todo class
        $objectIdentity = new ObjectIdentity('class', 'Mazenovi\TodoMVCBundle\Model\Todo');
        $acl = $aclProvider->createAcl($objectIdentity);
        $user = UserQuery::create()->findOneByUsername('todoadmin');
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $acl->insertClassAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        // todopublisher finisher todo class
        $user = UserQuery::create()->findOneByUsername('todofinisher');
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $acl->insertClassFieldAce('completed', $securityIdentity, MaskBuilder::MASK_EDIT);
        $aclProvider->updateAcl($acl);

        // ROLE_ADMIN owns Todo Class
        $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $acl->insertClassAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        // todopublisher publisher todo 1
        $todo = TodoQuery::create()->findOneByTitle('My Todo');
        $objectIdentity = ObjectIdentity::fromDomainObject($todo);
        $user = UserQuery::create()->findOneByUsername('todo1finisher');
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $acl = $aclProvider->findAcl($objectIdentity);        
        $acl->insertObjectFieldAce('completed', $securityIdentity, MaskBuilder::MASK_EDIT);
        $aclProvider->updateAcl($acl);

        

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

        $command = $this->getApplication()->find('propel:acl:init');
        $arguments = array(
            'command' => 'propel:acl:init',
            '--force'  => true,
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

        // super user every access granted
        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'admin',
            'email' => 'admin@m4z3.me',
            'password' => 'admin',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        $command = $this->getApplication()->find('fos:user:promote');
        $arguments = array(
            'command' => 'fos:user:promote',
            'username' => 'admin',
            'role' => 'ROLE_ADMIN',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        // owner of todo class
        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'todoadmin',
            'email' => 'todoadmin@m4z3.me',
            'password' => 'todoadmin',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        // publisher of todo class
        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'todofinisher',
            'email' => 'todofinisher@m4z3.me',
            'password' => 'todofinisher',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        // publisher of todo 1 object
        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command' => 'fos:user:create',
            'username' => 'todo1finisher',
            'email' => 'todo1finisher@m4z3.me',
            'password' => 'todo1finisher',
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

    }
    
}