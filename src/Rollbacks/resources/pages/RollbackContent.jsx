import Banner from '@wp-rollback/shared-core/components/Rollbacks/Banner';
import PluginInfo from '@wp-rollback/shared-core/components/Rollbacks/PluginInfo';
import MetaInfo from '@wp-rollback/shared-core/components/Rollbacks/MetaInfo';
import VersionsList from '@wp-rollback/shared-core/components/Rollbacks/VersionsList';
import RollbackThumbnail from '@wp-rollback/shared-core/components/Rollbacks/RollbackThumbnail';
import { useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';

/**
 * RollbackContent Component - Free version
 *
 * Displays rollback information for WordPress.org plugins and themes.
 *
 * @return {JSX.Element} The rollback content component
 */
const RollbackContent = () => {
    const {
        type,
        rollbackInfo,
        currentVersion,
        rollbackVersion,
        setRollbackVersion,
        setIsModalOpen,
        setModalTemplate,
    } = useRollbackContext();

    return (
        <div className="wpr-content">
            <Banner rollbackInfo={ rollbackInfo } type={ type } />
            <div className="wpr-content-header">
                <RollbackThumbnail rollbackInfo={ rollbackInfo } type={ type } />
                <PluginInfo rollbackInfo={ rollbackInfo } type={ type } currentVersion={ currentVersion } />
                <MetaInfo
                    rollbackInfo={ rollbackInfo }
                    type={ type }
                    currentVersion={ currentVersion }
                    setIsModalOpen={ setIsModalOpen }
                    setModalTemplate={ setModalTemplate }
                />
            </div>
            <VersionsList
                versions={ rollbackInfo?.versions }
                rollbackVersion={ rollbackVersion }
                setRollbackVersion={ setRollbackVersion }
                currentVersion={ currentVersion }
            />
        </div>
    );
};

export default RollbackContent;
