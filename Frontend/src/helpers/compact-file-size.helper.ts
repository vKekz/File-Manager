import { SizeUnit } from "../enums/size-unit.enum";

export function calculateCompactFileSize(bytes: number) {
  const kiloByte = 1024;
  let index = 0;

  if (bytes < kiloByte) {
    return `${bytes} ${SizeUnit[index]}`;
  }

  let convertedSize = bytes;
  while (convertedSize > kiloByte) {
    convertedSize /= kiloByte;
    index++;
  }

  const sizeUnit = SizeUnit[index];
  return `${convertedSize.toFixed(2)} ${sizeUnit}`;
}
