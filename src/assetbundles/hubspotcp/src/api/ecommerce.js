/* global Craft */

import axios from 'axios'

export default {
    saveStore(id, label, adminUri) {
        return axios.post(Craft.getActionUrl('hubspot-toolbox/ecommerce/save-store'),
            {
                id,
                label,
                adminUri
            },
            {
                headers: {
                    'X-CSRF-Token': Craft.csrfTokenValue,
                }
            });
    },

    getObjectMappings(mapper, sourceTypeId, previewObjectId) {
        return axios.get(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/get-object-mappings', {mapper, sourceTypeId, previewObjectId}))
    },

    saveObjectMapping(mapping, mapper, sourceTypeId, previewObjectId = null) {
        return axios.post(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/save-object-mapping'), {
            mapping,
            mapper,
            sourceTypeId,
            previewObjectId
        }, {
            headers: {
                'X-CSRF-Token': Craft.csrfTokenValue,
            }
        })
    },

    publishObjectMappings(mapper, sourceTypeId) {
        return axios.post(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/publish-object-mapping'), {
            mapper,
            sourceTypeId
        }, {
            headers: {
                'X-CSRF-Token': Craft.csrfTokenValue,
            }
        })
    },
}