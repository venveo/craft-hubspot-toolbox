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

    getObjectMappings(type) {
        return axios.get(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/get-object-mappings', {objectType: type}))
    },

    saveObjectMapping(mapping, previewElementId) {
        return axios.post(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/save-object-mapping'), {
            mapping: mapping,
            previewElementId: previewElementId
        }, {
            headers: {
                'X-CSRF-Token': Craft.csrfTokenValue,
            }
        })
    },

    publishObjectMappings(objectType) {
        return axios.post(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/publish-object-mapping'), {
            objectType: objectType
        }, {
            headers: {
                'X-CSRF-Token': Craft.csrfTokenValue,
            }
        })
    },

    getPreview(objectType, orderId = null) {
        return axios.get(Craft.getActionUrl('hubspot-toolbox/object-property-mapping/get-mapping-preview', {objectType: objectType, orderId: orderId}))
    }
}