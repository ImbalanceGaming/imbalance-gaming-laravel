@servers(['web' => 'envoy@192.168.0.2'])

@task('createDev')
    sudo useradd {{$user}} -m -s /bin/bash -p {{$user}}.password1
    sudo adduser {{$user}} sudo
    sudo mkdir /home/{{$user}}/sites
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

@task('install')
    cd /var/www/
    git clone {{$repo}} {{$deployLocation}}
@endtask
