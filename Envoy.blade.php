@servers(['web' => $server])

@setup
    $user = isset($user) ? $user : '';
    $command = isset($command) ? $command : '';
    $repo = isset($repo) ? $repo : '';
    $deployLocation = isset($deployLocation) ? $deployLocation : '';
    $baseDeployDir = '/var/www/';
    $devBaseDeployDir = '/home/';
@endsetup

@task('createDev')
    sudo useradd {{$user}} -m -s /bin/bash -p {{$user}}.password1
    sudo adduser {{$user}} sudo
    sudo mkdir /home/{{$user}}/sites
    sudo chown {{$user}}:{{$user}} /home/{{$user}}/sites
    sudo touch /home/{{$user}}/sites/index.html
    sudo ln -s /home/{{$user}}/sites/ /var/www/dev/{{$user}}
    echo "Added development area for {{$user}}"
@endtask

@task('removeDev')
    sudo rm /var/www/dev/{{$user}}
    sudo userdel {{$user}}
    sudo rm -rf /home/{{$user}}
    echo "Removed development area for {{$user}}"
@endtask

@task('runCommand')
    cd /var/www/{{$deployLocation}}
    {{$command}}
    echo "Command {{$command}} completed"
@endtask

@task('gitPull')
    cd /var/www/{{$deployLocation}}
    git pull
@endtask

@task('install')
    git clone {{$repo}} {{$baseDeployDir}}{{$deployLocation}}
    sudo chown www-data:www-data {{$baseDeployDir}}{{$deployLocation}}
@endtask

@task('installForDev')
    sudo git clone {{$repo}} {{$devBaseDeployDir}}{{$user}}/sites/{{$deployLocation}}
    sudo chown -R {{$user}}:{{$user}} {{$devBaseDeployDir}}{{$user}}/sites/{{$deployLocation}}
@endtask

@task('removeForDev')
    sudo rm -R {{$devBaseDeployDir}}{{$user}}/sites/{{$deployLocation}}
@endtask

@task('runCommandForDev')
    cd /home/{{$user}}/sites/{{$deployLocation}}
    sudo su {{$user}}
    {{$command}}
    echo "Command {{$command}} completed"
@endtask
