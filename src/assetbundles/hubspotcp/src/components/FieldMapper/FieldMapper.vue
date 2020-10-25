<template>
  <div class="flex">
    <button class="btn">Refresh Properties</button>
    <button class="btn">Preview</button>
  </div>
  <div class="input ltr">
    <table id="sites" class="editable fullwidth">
      <thead>
      <tr>
        <th scope="col" class="heading-cell thin">HubSpot Property Field</th>
        <th scope="col" class="type-channel type-structure singleline-cell textual has-info" style="">
          Object Template
        </th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <th scope="row" class="heading-cell thin">Name</th>
        <td class="type-channel type-structure singleline-cell textual has-info code">
            <textarea rows="1" placeholder="Enter object template"
                      style="min-height: 36px;"
                      tabindex="0">{% if object.level &gt; 1 %}{parent.uri}/{% endif %}{slug}</textarea>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../../api/ecommerce.js'
export default {
  name: 'FieldMapper',
  props: {
    objectType: String
  },
  data() {
    return {}
  },
  mounted() {
    api.getObjectMappings('contact').then((res) => {
      console.log(res);
    })
  },
  methods: {}
}
</script>
