#!/bin/sh
MYDIR="$( /usr/bin/dirname $0 )"
MYPATH="$( /bin/realpath ${MYDIR} )"

. /etc/rc.conf

workdir="${cbsd_workdir}"

set -e
. ${workdir}/cbsd.conf
. ${subr}
set +e

MYPATH="/var/db/webdev/virtual_private_net.sqlite"

[ -f "${MYPATH}" ] && /bin/rm -f "${MYPATH}"

/usr/local/bin/cbsd ${miscdir}/updatesql ${MYPATH} /usr/local/cbsd/share/webdev-virtual_private_net.schema vpnet

#/usr/local/bin/sqlite3 ${MYPATH} << EOF
#BEGIN TRANSACTION;
#INSERT INTO authkey ( name, authkey ) VALUES ( "oleg_ginzburg.pub", "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDeu6F22UHSFxkZeLpfToeVUhR4BY3FMbfF6R+qLbJ3MFFxPSfu27dxIexvliQKsBCyVAqxvxGYxHjehBN7mFJ1bewQRECNjpeLv+oOv28qiAYRWejYpkLAvhvTFZSUsJtam0H4fqVXCjb3CaoSUAe9V1fiNyR3q9o8F32oGMvbnZbj5CfwYLa+XT5nyc90++dMl1vMCdnPbYsI1svxrAmORvhxaoCGGP657F/5Cpq9vhp72M3BLkVuNvo2F0paLczXsw+2YIKfh7tBNIHLjBGFTCgGIm5ZKL4dmwaNhxhkpHpeWJnA9Memtk9NTxqEFeNGLhxsXGE8dEuY6TsKVcMb oleg@gizmo.my.domain");
#COMMIT;
#EOF

chmod 0777 ${MYPATH}
