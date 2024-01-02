export function formatDate(d, type) {
    const time = new Date(new Date(d).getTime());
    const date = new Date(d);

    let day = date.getDate();
    if (day < 10) day = `0${day}`;

    let month = date.getMonth() + 1;
    if (month < 10) month = `0${month}`;

    const hours =
        time.getHours() < 10 ? `0${time.getHours()}` : time.getHours();
    const minutes =
        time.getMinutes() < 10 ? `0${time.getMinutes()}` : time.getMinutes();
    const seconds =
        time.getSeconds() < 10 ? `0${time.getSeconds()}` : time.getSeconds();

    if (type === "time") return `${hours}:${minutes}:${seconds}`;
    if (type === "date") return `${day}-${month}-${date.getFullYear()}`;

    return `${day}-${month}-${date.getFullYear()} (${hours}:${minutes}:${seconds})`;
}
