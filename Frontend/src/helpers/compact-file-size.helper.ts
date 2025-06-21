import { SizeUnit } from "../enums/size-unit.enum";

export function calculateCompactFileSize(bytes: number) {
  const kiloByte = 1000;
  if (bytes < kiloByte) {
    return `${bytes} ${SizeUnit[SizeUnit.Bytes]}`;
  }

  let convertedSize = bytes;
  let index = 0;
  while (convertedSize > kiloByte) {
    convertedSize /= kiloByte;
    index++;
  }

  const sizeUnit = SizeUnit[index];
  return `${convertedSize.toFixed(2)} ${sizeUnit}`;
}
