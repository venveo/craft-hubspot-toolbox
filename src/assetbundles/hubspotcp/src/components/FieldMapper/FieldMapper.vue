<template>
  <div class="flex">
    <button class="btn icon add" @click="showingPropertyPicker = !showingPropertyPicker">Add Property</button>
    <button class="btn submit" @click.prevent="publishChanges">Publish Changes</button>
  </div>
  <div v-if="showingPropertyPicker">
    <div class="pane flex">
      <div class="select">
        <select v-model="selectedPropertyToAdd">
          <option v-for="(item, idx) in unmappedData" :value="idx">{{ item.objectProperty.label }} -
            ({{ item.property }})
          </option>
        </select>
      </div>
      <div>
        <button class="btn icon add" @click.prevent="handleAddProperty">Add</button>
      </div>
    </div>
    <hr>
  </div>
  <div class="input ltr">
    <table id="sites" class="editable fullwidth">
      <thead>
      <tr>
        <th scope="col" class="heading-cell thin">HubSpot Property</th>
        <th scope="col" class="singleline-cell textual" style="">Object Template</th>
        <th scope="col" class="singleline-cell textual" style="">Preview</th>
        <th colspan="1"></th>
      </tr>
      </thead>
      <tbody>
      <mapped-property v-for="(mapping, idx) in mappedProperties" v-model:template="mappedProperties[idx].template"
                       :mapping="mapping" v-on:delete="deletePropertyMapping(mapping)" @input="handleTemplateChange(mapping)"/>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../../api/ecommerce.js'
import MappedProperty from "./MappedProperty.vue";

export default {
  name: 'FieldMapper',
  components: {MappedProperty},
  props: {
    objectType: String,
    context: String
  },
  data() {
    return {
      showingPropertyPicker: false,
      propertyMappings: [],
      unmappedData: [],
      mappedProperties: [],

      selectedPropertyToAdd: null
    }
  },
  mounted() {
    this.fetchMappings();
  },
  methods: {
    fetchMappings() {
      api.getObjectMappings(this.objectType, this.context).then((res) => {
        this.propertyMappings = res.data;
        this.unmappedData = this.propertyMappings.filter(function (item) {
          return item.id === null
        })
        this.mappedProperties = this.propertyMappings.filter(function (item) {
          return item.id !== null
        })
      })
    },
    handleAddProperty: async function () {
      await api.saveObjectMapping(this.unmappedData[this.selectedPropertyToAdd]);
      this.fetchMappings();
    },
    handleObjectTemplateChanged(mapped, e) {
      const index = this.propertyMappings.findIndex((e) => {
        return e.id === mapped.id;
      })
      this.propertyMappings[index].template = e.target.value
    },
    async saveMapping(mapped) {
      await api.saveObjectMapping(mapped);
      this.fetchMappings();
    },
    async publishChanges() {
      await api.publishObjectMappings(this.objectType, this.context);
      alert('Published');
      this.fetchMappings();
    },
    handleTemplateChange(mapping) {
      api.saveObjectMapping(mapping).then(v => {
        const mappingData = v.data
        mapping.preview = mappingData.preview
      });
    },
    async deletePropertyMapping(mapping) {
      console.log('Need to delete', mapping)
    }
  }
}
</script>
