class puphpet::python::pip {

  Exec { path => [ '/usr/bin/', '/usr/local/bin', '/bin', '/usr/local/sbin', '/usr/sbin', '/sbin' ] }

  exec { 'install_setuptools':
    command => "curl https://bootstrap.pypa.io/ez_setup.py | python",
    cwd     => '/tmp',
    before  => Exec['easy_install pip']
  }

  exec { 'easy_install pip':
    unless  => 'which pip',
  }

  if $::osfamily == 'RedHat' {
    exec { 'rhel pip_provider_name_fix':
      command     => 'alternatives --install /usr/bin/pip-python pip-python /usr/bin/pip 1',
      subscribe   => Exec['easy_install pip'],
      unless      => 'which pip-python',
    }
  }

}
