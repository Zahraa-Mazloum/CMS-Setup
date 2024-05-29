<?php

return function () {
    if (tier()->through == "http") {
        return [];
    }
    return [];
};
