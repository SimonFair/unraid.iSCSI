#!/bin/bash

function avail_iqns(){
echo -n "$(targetcli ls iscsi 1 | tail -n +2 | cut -d ' ' -f4- | cut -d ' ' -f1)"
}

function create_iqn(){
echo -n "$(targetcli iscsi/ create)" | head -1 | cut -d ' ' -f3- | rev | cut -d '.' -f2- | rev
}

function delete_iqn(){
echo -n "$(targetcli /iscsi delete $1)"
}

function delete_fileIO(){
echo -n "$(targetcli backstores/fileio delete name=$1)"
}

function delete_blockVOL(){
echo -n "$(targetcli backstores/block delete name=$1)"
}

function avail_fileio(){
echo -n "$(targetcli ls backstores 2 | awk '/pscsi/{f=0} f; /fileio/{f=1}' | cut -d ' ' -f5)"
}

function create_fileIO(){
if [ ! -d $2 ]; then
	mkdir -p $2
fi
echo -n "$(targetcli backstores/fileio create $1 $2/$1.img $3 write_back=$4) >/tmp/file"
}

function avail_blockvols(){
echo -n "$(targetcli ls backstores 2 | awk '/fileio/{f=0} f; /block/{f=1}' | cut -d ' ' -f5)"
}

function blockvol_names(){
echo -n "$(targetcli ls backstores 2 | awk '/fileio/{f=0} f; /block/{f=1}' | cut -d ' ' -f5)"
}

function create_blockvol(){
echo -n "$(targetcli backstores/block create name=$2 dev=/dev/disk/by-id/$1)"
}

function create_block_lun(){
echo -n "$(targetcli iscsi/$1/tpg1/luns/ create /backstores/block/$2)"
}

function create_fileio_lun(){
echo -n "$(targetcli iscsi/$1/tpg1/luns/ create /backstores/fileio/$2)"
}

function create_pscsi_lun(){
echo -n "$(targetcli iscsi/$1/tpg1/luns/ create /backstores/fileio/$2)"
}

function create_acl(){
echo -n "$(targetcli iscsi/$1/tpg1/acls/ create $2)"
}

function delete_acl(){
echo -n "$(targetcli iscsi/$1/tpg1/acls/ delete $2)"
}

function get_available_disks(){
echo -n "$(grep -vf <(cat /usr/local/emhttp/state/disks.ini | grep -w 'id=' | grep -wv 'id=""' | cut -d '"' -f2) <(ls /dev/disk/by-id/) | grep -vf <(echo "wwn") | uniq -w 25)"
}

$@
