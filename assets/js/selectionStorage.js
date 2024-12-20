document.addEventListener("DOMContentLoaded", function () {
    let equipmentSelected = false;
    let equipment = document.getElementById("equipment");
    let buildings = document.querySelectorAll(".building");
    console.log(1);

    let handlerMouseMove = function (event) {
        event.preventDefault();
        equipment.style.left = event.clientX + "px";
        equipment.style.top =
            event.clientY -
            equipment.getBoundingClientRect().height / 2 -
            document.getElementById("containerStorage").getBoundingClientRect().y +
            "px";
    };
    equipment.addEventListener("mousedown", function (event) {
        event.preventDefault();
        equipmentSelected = true;
        equipment.style.opacity = 0.5;
        equipment.addEventListener("mousemove", handlerMouseMove);
        equipment.addEventListener("mouseup", () => {
            equipmentSelected = false;
            equipment.style.opacity = 1;
            equipment.removeEventListener("mousemove", handlerMouseMove);
        });
    });

    buildings.forEach((building) => {
        building.addEventListener("mouseenter", function () {
            if (equipmentSelected) {
                building.classList.add("selected");
                equipment.style.zIndex = 30;
                building.querySelector(".emplacements").style.display = "block";
                building.querySelector(".changeBuilding").style.display = "block";
            }
        });

        building.querySelector(".changeBuilding").addEventListener("", function () {
            if (equipmentSelected) {
                building.classList.remove("selected");
                equipment.style.zIndex = 10;
                building.querySelector(".emplacements").style.display = "none";
                building.querySelector(".changeBuilding").style.display = "none";
            }
        });
    });
});
