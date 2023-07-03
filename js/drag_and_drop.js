var dragItems = document.querySelectorAll(".drag-item");
var dropAreas = document.querySelectorAll(".drop-area-card");
var droppedItems = [];

dragItems.forEach(function (dragItem) {
  dragItem.addEventListener("dragstart", function (event) {
    if (!isItemDropped(dragItem.innerText)) {
      event.dataTransfer.setData("text", event.target.innerText);
    } else {
      event.preventDefault();
    }
  });
});

dropAreas.forEach(function (dropArea) {
  dropArea.addEventListener("dragover", function (event) {
    event.preventDefault();
  });

  dropArea.addEventListener("drop", function (event) {
    event.preventDefault();

    // Only allow to drop if the drop area is empty or contains only a <span> element
    if (!isDropAreaValid(event.target)) {
      return;
    }

    var data = event.dataTransfer.getData("text");
    // Drop the item only if it was not dropped before
    if (isItemDropped(data)) {
      return;
    }

    // Add the item to the dropped items array
    droppedItems.push(data);

    // Add the item to the drop area
    event.target.innerText = data;

    // set the value of the input with id resposta_1 for example
    var dropAreaNumber = event.target.classList[1].split("-")[3];
    document.getElementById("resposta_" + dropAreaNumber).value = data;

    // Show the remove button
    var removeButton = document.createElement("button");
    removeButton.classList.add("btn", "btn-danger", "remove-btn");
    removeButton.innerHTML = "&times;";
    removeButton.addEventListener("click", function () {
      event.target.innerText = "";
      removeDroppedItem(data);
      this.remove();

      var span = document.createElement("span");
      span.style.fontSize = "24px";
      var dropAreaNumber = event.target.classList[1].split("-")[3];
      span.innerText = dropAreaNumber;
      event.target.appendChild(span);

      // and so on / insert in input hidden
      var dropAreaNumber = event.target.classList[1].split("-")[3];
      document.getElementById("resposta_" + dropAreaNumber).value = "";
    });

    event.target.appendChild(removeButton);
  });
});

function isItemDropped(itemText) {
  return droppedItems.includes(itemText);
}

function removeDroppedItem(itemText) {
  var index = droppedItems.indexOf(itemText);
  if (index !== -1) {
    droppedItems.splice(index, 1);
  }
}

function isDropAreaValid(dropArea) {
  // Check if the drop area is empty or contains only a <span> element
  return (
    dropArea.innerText === "" ||
    (dropArea.childElementCount === 1 &&
      dropArea.firstElementChild.tagName === "SPAN")
  );
}
