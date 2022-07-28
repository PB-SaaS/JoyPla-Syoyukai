#!/bin/bash
#
#
#java -jar custom_module.jar -t $TOKEN -s $SECRET $*

TARGET_DIR='src'
TARGET='NewJoyPlaTenantAdmin'
DATE=`date '+%y%m%d%H%M%S'`
LOG_OUT="logs/deploy.log"
ZIP_FILE="upload_file"

log() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][INFO] $@" | tee -a ${LOG_OUT}
}

errorlog() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][ERROR] $@" | tee -a ${LOG_OUT}
    exit
}

if [ -e logs ] ;then
    log "directory logs"
else 
    if mkdir logs ;then
        log "make directory logs"
    else
        errorlog "make directory logs"
    fi
fi

if [ -e tmp ] ;then
    log "directory tmp"
else 
    if mkdir tmp ;then
        log "make directory tmp"
    else
        errorlog "make directory tmp"
    fi
fi

if [ -e tmp/${TARGET} ] ;then
    if rm -rf tmp/${TARGET} ;then
        log "rm directory ${TARGET}"
    else
        errorlog "rm directory ${TARGET}"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi
<< COMMENTOUT

if cp -r ../${TARGET_DIR}/${TARGET} tmp ;then
   log "cp directory ${TARGET}"
else
    errorlog "cp directory ${TARGET}"
fi
COMMENTOUT

if php makeAutoloadAdmin.php "../src/${TARGET}/require.php" ;then
    log "makeAutoload ${TARGET}"
else 
    errorlog "makeAutoload ${TARGET}"
fi

cd ../src/

if git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/ ;then
    cd -
    log "git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/"
else
    cd -
    errorlog "git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/"
fi

cd tmp
if zip -r ${ZIP_FILE} ${TARGET} ;then
    cd -
    log "zip directory ${TARGET}"
else
    cd -
    errorlog "zip directory ${TARGET}"
fi

if rm -rf tmp/${TARGET} ;then
    log "rm directory ${TARGET}"
else
    errorlog "rm directory ${TARGET}"
fi

cd tmp
if php ../upload.php ${ZIP_FILE}.zip ;then
    cd -
    log "upload ${TARGET}"
else 
    cd -
    errorlog "upload ${TARGET}"
fi

if [ -e tmp/${TARGET} ] ;then
    if rm -rf tmp/${TARGET} ;then
        log "rm directory ${TARGET}"
    else
        errorlog "rm directory ${TARGET}"
    fi
fi

cd ../


if git add ${TARGET_DIR}/${TARGET}/. ;then
    cd -
    log "git add ${TARGET_DIR}/${TARGET}/."
else
    cd -
    errorlog "git add ${TARGET_DIR}/${TARGET}/."
fi

#if java -jar custom_module.jar -t $TOKEN -s $SECRET tmp/upload_data.zip ;then
#    log "upload ${TARGET}"
#else 
#    errorlog "upload ${TARGET}"
#fi

exit;