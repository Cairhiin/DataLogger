export function formatDate(d, type) {
    const time = new Date(new Date(d).getTime());
    const date = new Date(d).toLocaleDateString("nl-NL", {
        day: "numeric",
        month: "numeric",
        year: "numeric",
    });

    const hours =
        time.getHours() < 10 ? `0${time.getHours()}` : time.getHours();
    const minutes =
        time.getMinutes() < 10 ? `0${time.getMinutes()}` : time.getMinutes();
    const seconds =
        time.getSeconds() < 10 ? `0${time.getSeconds()}` : time.getSeconds();

    if (type === "date") return `${hours}:${minutes}:${seconds}`;

    return `${date} (${hours}:${minutes}:${seconds})`;
}
