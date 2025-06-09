export interface DirectoryDto {
  id: string;
  parentId: string;
  name: string;
  path: string;
  isRoot: boolean;
}

export interface DirectoryDtoWithChildren extends DirectoryDto {
  children: DirectoryDto[];
}
