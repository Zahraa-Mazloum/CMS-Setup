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
  <MDBNavbar expand="lg" light>
    <!-- Toggle button -->
    <MDBNavbarToggler
      target="#navbarRightAlignExample"
      @click="collapse5 = !collapse5"
    ></MDBNavbarToggler>
    <!-- Collapsible wrapper -->
    <MDBCollapse v-model="collapse5" id="navbarRightAlignExample">
      <MDBNavbarNav right class="mb-2 mb-lg-0">
        <!-- Right links -->
        <MDBNavbarItem to="#" active>
          Home
        </MDBNavbarItem>
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
            <MDBDropdownItem href="#"
              >Something else here</MDBDropdownItem
            >
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