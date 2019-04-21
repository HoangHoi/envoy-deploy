@servers(['local' => '127.0.0.1', 'server' => ['user@192.168.1.1']])

@setup
    $path = __DIR__;
    $now = new DateTime();
    $gitRepo = 'git@github.com:HoangHoi/envoy-auto-deploy.git';
    $defaultBrand = 'master';
    $release = 'releases/' . $now->format('Y-m-d_H-i-s');

    $symlinks = [
        'storage/app/public/avatars',
        'storage/framework/sessions',
        'storage/logs',
    ];
@endsetup

@story('deploy', ['on' => 'local'])
    git
    link-share
    install
    run-script
    change-permissions
    update-current
@endstory

@task('git')
    git clone -b {{ $branch ?? $defaultBrand }} "{{ $gitRepo }}" "{{ $release }}"
@endtask

@task('link-share')
    @foreach ($symlinks as $symlink)
        @if (!file_exists('share/' . $symlink))
            mkdir -p share/{{ $symlink }}
        @endif

        rm -rf {{ $release }}/{{ $symlink }}
        ln -nfs {{ $path }}/share/{{ $symlink }} {{ $release }}/{{ $symlink }}
    @endforeach

    echo "All symlinks have been set"

    @if (!file_exists('share/.env'))
        cp "{{ $release . '/.env.example' }}" "share/.env"
    @endif

    ln -nfs {{ $path }}/share/.env {{ $release }}/.env
@endtask

@task('install')
    cd {{ $release }}
    composer install --no-interaction

    php ./artisan key:generate
    php ./artisan migrate --force
    php ./artisan storage:link
@endtask

@task('run-script')
    cd {{ $release }}
    yarn install --non-interactive
    yarn prod
@endtask

@task('change-permissions')
    chmod 777 -R {{ $release }}/storage
    chmod 777 -R {{ $path }}/share/storage
    chmod 777 -R {{ $release }}/bootstrap/cache
@endtask

@task('update-current', ['on' => $on])
	rm -rf {{ $path }}/current
	ln -nfs {{ $path }}/{{ $release }} {{ $path }}/current
@endtask
