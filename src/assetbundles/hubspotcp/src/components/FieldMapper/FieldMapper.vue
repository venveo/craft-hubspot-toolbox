<!--
  -  @link      https://www.venveo.com
  -  @copyright Copyright (c) 2020 Venveo
  -->

<template>
  <div class="flex">
    <button class="btn icon add" @click="showingPropertyPicker = !showingPropertyPicker">Add Property</button>
    <button class="btn icon search" :class="{'disabled': loadingNewPreview}" :disabled="loadingNewPreview" @click="updatePreview">New Preview</button>
    <button class="btn submit" @click.prevent="publishChanges">Publish Changes</button>
    <div class="flex-grow"></div>
    <div v-if="sourceTypeName"><label>{{sourceTypeName}}</label></div>
    <div v-if="sourceTypes" class="select">
      <select v-model="sourceTypeId" @change="handleSourceTypeChanged">
        <option :value="null">Default</option>
        <option v-for="(sourceType, id) in sourceTypes" :value="id">{{sourceType.displayName}}</option>
      </select>
    </div>
  </div>
  <div v-if="showingPropertyPicker">
    <div class="pane flex">
      <div class="select">
        <select v-model="selectedPropertyToAdd">
          <option disabled selected value="">Select Property</option>
          <option v-for="(data, name) in properties" :value="name" :disabled="Object.keys(propertyMappings).includes(name)">{{ data.label }} - ({{ name }})</option>
        </select>
      </div>
      <div>
        <button class="btn icon add" :class="{'disabled': loadingAddProperty, 'loading': loadingAddProperty}" :disabled="loadingAddProperty" @click.prevent="handleAddProperty">Add</button>
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
      <mapped-property v-for="(mapping, name) in propertyMappings"
                       v-model:template="propertyMappings[name].template"
                       :property="properties[name]"
                       :preview="propertyMappings[name].renderedValue"
                       v-on:input="(e) => {handleTemplateChange(propertyMappings[name])}"
                       v-on:delete="deletePropertyMapping(propertyMappings[name])"
      />
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../../api/ecommerce.js'
import MappedProperty from "./MappedProperty.vue";
import {debounce} from "lodash"

export default {
  name: 'FieldMapper',
  components: {MappedProperty},
  props: {
    mapper: String
  },
  data() {
    return {
      showingPropertyPicker: false,
      propertyMappings: [],
      properties: [],

      selectedPropertyToAdd: '',
      previewObjectId: null,

      loadingNewPreview: false,
      loadingAddProperty: false,

      sourceTypeId: null,

      sourceTypes: null,
      sourceTypeName: null
    }
  },
  mounted() {
    this.fetchMappings().then(() => {})
  },
  methods: {
    async fetchMappings() {
      const {data} = await api.getObjectMappings(this.mapper, this.sourceTypeId, this.previewObjectId)
      this.properties = data.properties
      this.propertyMappings = data.propertyMappings
      if (Object.keys(data).includes('sourceTypes')) {
        this.sourceTypes = data.sourceTypes
        this.sourceTypeName = data.sourceTypeName
      }
      if (!this.previewObjectId) {
        this.previewObjectId = data.sourceId
      }
    },
    handleSourceTypeChanged: async function() {
      await this.fetchMappings();
    },
    handleAddProperty: async function () {
      this.loadingAddProperty = true
      const mapping = {
        property: this.selectedPropertyToAdd,
      }
      await api.saveObjectMapping(mapping, this.mapper, this.sourceTypeId, this.previewObjectId);
      await this.fetchMappings();
      this.loadingAddProperty = false
      this.selectedPropertyToAdd = '';
    },
    handleObjectTemplateChanged(mapped, e) {
      const index = this.propertyMappings.findIndex((e) => {
        return e.id === mapped.id;
      })
      this.propertyMappings[index].template = e.target.value
    },
    async saveMapping(mapped) {
      await api.saveObjectMapping(mapped, this.mapper, this.sourceTypeId, this.previewObjectId);
      this.fetchMappings();
    },
    async publishChanges() {
      await api.publishObjectMappings(this.mapper, this.sourceTypeId);
      await this.fetchMappings();
      alert('Published');
    },
    handleTemplateChange: debounce(function(mapping) {
      api.saveObjectMapping(mapping, this.mapper, this.sourceTypeId, this.previewObjectId).then(v => {
        const mappingData = v.data
        mapping.renderedValue = mappingData.renderedValue
      });
    }, 250),
    async deletePropertyMapping(mapping) {
      console.log('Need to delete', mapping)
    },
    async updatePreview() {
      this.loadingNewPreview = true
      this.previewObjectId = null
      await this.fetchMappings()
      this.loadingNewPreview = false
    }
  }
}
</script>
