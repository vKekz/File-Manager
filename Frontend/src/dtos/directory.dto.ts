import { FileDto } from "./file.dto";

export interface DirectoryDto {
  id: string;
  parentId: string;
  name: string;
  path: string;
  createdAt: string;
  compactDate: string;
  isRoot: boolean;
}

export interface DirectoryDtoWithContent extends DirectoryDto {
  children: DirectoryDto[];
  files: FileDto[];
}
