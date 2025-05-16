/**
 * Filters visible room sections based on the user's search input.
 * Hides room sections that do not match the search query.
 */
function filterRooms() {
    const input = document.getElementById("roomSearch").value.toLowerCase();
    const rooms = document.querySelectorAll(".room-section");

    rooms.forEach(room => {
    const text = room.innerText.toLowerCase();
    room.style.display = text.includes(input) ? "flex" : "none";
    });
}