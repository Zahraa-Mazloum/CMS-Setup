<script setup lang="ts">
  import {
    MDBNavbar,
    MDBNavbarToggler,
    MDBNavbarBrand,
    MDBNavbarNav,
    MDBNavbarItem,
    MDBCollapse,
    MDBDropdown,
    MDBDropdownToggle,
    MDBDropdownMenu,
    MDBDropdownItem
  } from 'mdb-vue-ui-kit';
  import { ref } from 'vue';

  const collapse5 = ref(false);
  const dropdown8 = ref(false);
</script>

<template>
  <MDBNavbar classContainer="justify-content-start" expand="lg" light transparent class="custom-navbar">
    <MDBNavbarBrand href="#">Navbar</MDBNavbarBrand>

    <!-- Toggle button -->
    <MDBNavbarToggler  target="#nav" data-mdb-target="#nav" @click="collapse5 = !collapse5">
    </MDBNavbarToggler>
    <!-- Collapsible wrapper -->
    <MDBCollapse v-model="collapse5" id="nav">

      <MDBNavbarNav right class="mb-2 mb-lg-0" center> 
        <!-- Right links -->
        <MDBNavbarItem href="#" active>Home</MDBNavbarItem>
        <MDBNavbarItem href="#" active>
          Link
        </MDBNavbarItem>
        <!-- Navbar dropdown -->
        <MDBDropdown class="nav-item" v-model="dropdown8">
          <MDBDropdownToggle
            tag="a"
            class="nav-link"
            @click="dropdown8 = !dropdown8"
            >Dropdown</MDBDropdownToggle
          >
          <MDBDropdownMenu>
            <MDBDropdownItem href="#">Action</MDBDropdownItem>
            <MDBDropdownItem href="#">Another Action</MDBDropdownItem>
            <MDBDropdownItem href="#">Something else here</MDBDropdownItem>
          </MDBDropdownMenu>
        </MDBDropdown>
        <MDBNavbarItem
          to="#"
          disabled
          tabindex="-1"
          aria-disabled="true"
        >
          Disabled
        </MDBNavbarItem>
        <!-- Right links -->
      </MDBNavbarNav>
    </MDBCollapse>
    <!-- Collapsible wrapper -->
  </MDBNavbar>
</template>
<style scoped>
.custom-navbar {
  padding-left: 4% !important;
  --mdb-navbar-box-shadow:none !important;
  padding-right: 3% !important;

}
</style>