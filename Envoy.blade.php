@servers(['local' => '127.0.0.1', 'server' => ['user@192.168.1.1']])

@setup
    $now = new DateTime();
    $gitRepo = 'git@github.com:HoangHoi/envoy-auto-deploy.git';
    $defaultBrand = 'master';
    $release = $now->format('Y-m-d_H-i-s');

@endsetup

@story('deploy', ['on' => 'local'])
    git
    link-share
    install
    run-script
    change-permissions

@endstory

@task('git')
    git clone -b {{ $branch ?? $defaultBrand }} "{{ $gitRepo }}" "releases/{{ $release }}"
@endtask

@task('link-share')
    ls
@endtask

@task('install')
    ls
@endtask

@task('run-script')
    ls
@endtask

@task('change-permissions')
    ls
@endtask
