function filterRooms() {
    const input = document.getElementById("roomSearch").value.toLowerCase();
    const rooms = document.querySelectorAll(".room-section");

    rooms.forEach(room => {
    const text = room.innerText.toLowerCase();
    room.style.display = text.includes(input) ? "flex" : "none";
    });
}